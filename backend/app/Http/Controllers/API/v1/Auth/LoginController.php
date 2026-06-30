<?php

namespace App\Http\Controllers\API\v1\Auth;

use DB;
use Throwable;
use Exception;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Models\Notification;
use App\Helpers\ResponseError;
use Illuminate\Http\JsonResponse;
use Lcobucci\JWT\UnencryptedToken;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Auth\LoginRequest;
use Laravel\Sanctum\PersonalAccessToken;
use App\Http\Requests\FilterParamsRequest;
use Psr\SimpleCache\InvalidArgumentException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Http\Requests\Auth\PhoneVerifyRequest;
use App\Http\Requests\Auth\ProvideLoginRequest;
use App\Http\Requests\Auth\ReSendVerifyRequest;
use App\Services\AuthService\AuthByMobilePhone;
use App\Services\UserServices\UserWalletService;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Services\EmailSettingService\EmailSendService;

class LoginController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request): JsonResponse
    {
        if ($request->input('phone')) {
            return $this->loginByPhone($request);
        }

        if (!auth()->attempt($request->only(['email', 'password']))) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_102,
                'message' => __('errors.' . ResponseError::ERROR_102, locale: $this->language)
            ]);
        }

        $token = auth()->user()->createToken('api_token')->plainTextToken;

        return $this->successResponse('User successfully login', [
            'access_token'  => $token,
            'token_type'    => 'Bearer',
            'user'          => UserResource::make(auth('sanctum')->user()->loadMissing(['shop', 'model'])),
        ]);
    }

    protected function loginByPhone($request): JsonResponse
    {
        if (!auth()->attempt($request->only('phone', 'password'))) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_102,
                'message' => __('errors.' . ResponseError::ERROR_102, locale: $this->language)
            ]);
        }

        $token = auth()->user()->createToken('api_token')->plainTextToken;

        return $this->successResponse('User successfully login', [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => UserResource::make(auth('sanctum')->user()->loadMissing(['shop', 'model'])),
        ]);

    }

    /**
     * Obtain the user information from Provider.
     *
     * @param $provider
     * @param ProvideLoginRequest $request
     * @return JsonResponse
     */
    public function handleProviderCallback($provider, ProvideLoginRequest $request): JsonResponse
    {
        try {
            $this->validateProvider($request->input('id'));
        } catch (Throwable $e) {
            $this->error($e);

            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_107,
                'message' => __('errors.' . ResponseError::ERROR_107, locale: $this->language)
            ]);
        }

        try {
            $result = DB::transaction(function () use ($request, $provider) {

                @[$firstname, $lastname] = explode(' ', $request->input('name'));

                $user = User::withTrashed()->updateOrCreate(['email' => $request->input('email')], [
                    'email'             => $request->input('email'),
                    'email_verified_at' => now(),
                    'referral'          => $request->input('referral'),
                    'active'            => true,
                    'firstname'         => !empty($firstname) ? $firstname : $request->input('email'),
                    'lastname'          => $lastname,
                    'deleted_at'        => null,
                ]);

				if ($request->input('avatar')) {
					$user->update(['img' => $request->input('avatar')]);
				}

				$user->socialProviders()->updateOrCreate([
					'provider'      => $provider,
					'provider_id'   => $request->input('id'),
				], [
					'avatar' => $request->input('avatar')
				]);

                if (!$user->hasAnyRole(Role::query()->pluck('name')->toArray())) {
                    $user->syncRoles('user');
                }

                $ids = Notification::pluck('id')->toArray();

                if ($ids) {
                    $user->notifications()->sync($ids);
                } else {
                    $user->notifications()->forceDelete();
                }

                $user->emailSubscription()->updateOrCreate([
                    'user_id' => $user->id
                ], [
                    'active' => true
                ]);

				if (empty($user->wallet?->uuid)) {
					$user = (new UserWalletService)->create($user);
				}

                return [
                    'token' => $user->createToken('api_token')->plainTextToken,
                    'user'  => UserResource::make($user),
                ];
            });

            return $this->successResponse('User successfully login', [
                'access_token'  => data_get($result, 'token'),
                'token_type'    => 'Bearer',
                'user'          => data_get($result, 'user'),
            ]);
        } catch (Throwable $e) {
            $this->error($e);
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::USER_IS_BANNED, locale: $this->language)
            ]);
        }
    }

	/**
	 * @param FilterParamsRequest $request
	 * @return JsonResponse
	 */
	public function checkPhone(FilterParamsRequest $request): JsonResponse
	{
		$user = User::with('shop')
			->where('phone', $request->input('phone'))
			->first();

		if (!$user) {
			return $this->onErrorResponse([
				'code'    => ResponseError::ERROR_102,
				'message' => __('errors.' . ResponseError::ERROR_102, locale: $this->language)
			]);
		}

		$token = $user->createToken('api_token')->plainTextToken;

		return $this->successResponse('User successfully login', [
			'access_token' => $token,
			'token_type'   => 'Bearer',
			'user'         => UserResource::make($user),
		]);
	}

    public function logout(): JsonResponse
    {
        try {
            /** @var User $user */
            /** @var PersonalAccessToken $current */
            $user           = auth('sanctum')->user();
            $firebaseToken  = collect($user->firebase_token)
                ->reject(fn($item) => (string)$item == (string)request('firebase_token') || empty($item))
                ->toArray();

            $user->update([
                'firebase_token' => $firebaseToken
            ]);

			try {
				$token   = str_replace('Bearer ', '', request()->header('Authorization'));

				$current = PersonalAccessToken::findToken($token);
				$current->delete();

			} catch (Throwable $e) {
				$this->error($e);
			}
        } catch (Throwable $e) {
            $this->error($e);
        }

        return $this->successResponse('User successfully logout');
    }

    /**
     * @param $idToken
     * @return UnencryptedToken|bool
     * @throws Exception
     */
    public function validateProvider($idToken): UnencryptedToken|bool
    {
        $data = Firebase::auth()->verifyIdToken($idToken);
        $tokenEmail = $data->claims()->get('email');

        if (!$data->isExpired(now()) && $tokenEmail === request('email')) {
            return true;
        }

        throw new Exception('expired');
    }

    public function forgetPassword(ForgetPasswordRequest $request): JsonResponse
    {
        return (new AuthByMobilePhone)->authentication($request->validated());
    }

    public function forgetPasswordEmail(ReSendVerifyRequest $request): JsonResponse
    {
        $user = User::withTrashed()->where('email', $request->input('email'))->first();

        if(!$user) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language),
            ]);
        }

        $token = mb_substr((string)time(), -6, 6);

		$result = (new EmailSendService)->sendEmailPasswordReset($user, $token);

		if (!data_get($result, 'status')) {
			return $this->onErrorResponse($result);
		}

		$user->update([
			'verify_token' => $token
		]);

        return $this->successResponse('Verify code send');
    }

    public function forgetPasswordVerifyEmail(int $hash): JsonResponse
    {
        $user = User::withTrashed()->where('verify_token', $hash)->first();

        if (!$user) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::USER_NOT_FOUND, locale: $this->language)
            ]);
        }

        if (!$user->hasAnyRole(Role::query()->pluck('name')->toArray())) {
            $user->syncRoles('user');
        }

        $token = $user->createToken('api_token')->plainTextToken;

        $user->update([
            'active'       => true,
            'deleted_at'   => null,
			'verify_token' => null
		]);

		try {
			Cache::delete($hash);
		} catch (InvalidArgumentException) {}

        return $this->successResponse('User successfully login', [
            'token' => $token,
            'user'  => UserResource::make($user),
        ]);
    }

    /**
     * @param PhoneVerifyRequest $request
     * @return JsonResponse
     */
    public function forgetPasswordVerify(PhoneVerifyRequest $request): JsonResponse
    {
        return (new AuthByMobilePhone)->forgetPasswordVerify($request->validated());
    }


}
