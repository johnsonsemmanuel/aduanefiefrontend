<?php

namespace App\Services\PaymentService;

use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Models\Subscription;
use DB;
use Exception;
use Http;
use Illuminate\Database\Eloquent\Model;
use Mollie\Api\MollieApiClient;
use Str;
use Throwable;

class MollieService extends BaseService
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    /**
     * @param array $data
     * @return PaymentProcess|Model
     * @throws Exception
     */
    public function orderProcessTransaction(array $data): Model|PaymentProcess
    {
        $payment        = Payment::where('tag', Payment::TAG_MOLLIE)->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $host = request()->getSchemeAndHttpHost();

        $token = base64_encode(data_get($payload, 'secret_key'));

        $headers = [
            'Authorization' => "Basic $token"
        ];

		[$key, $before] = $this->getPayload($data, $payload);
		$modelId 		= data_get($before, 'model_id');

		$totalPrice = round((float)data_get($before, 'total_price'));

		$url  = "$host/order-stripe-success?$key=$modelId";

		$currencies = [
			'BRL' => 'BRL',
			'MXN' => 'MXN',
			'MYR' => 'MYR',
			'THB' => 'THB',
			'TWD' => 'TWD',
			'EUR' => 'EUR',
		];

		$currency = Str::upper(data_get($before, 'currency'));

		if (!in_array($currency, $currencies)) {
			throw new Exception("Support only: BRL, MXN, MYR, THB, TWD, EUR");
		}

//        $mollie = new MollieApiClient;
//        $mollie->setApiKey(data_get($payload, 'secret_key'));

//        $paymentLink = $mollie->paymentLinks->create([
//            "description" => "Payment for products",
//            "amount" => [
//                "currency" => $currency,
//                "value" => "$totalPrice",
//            ],
//            "redirectUrl" => $url,
//            "webhookUrl"  => "$host/api/v1/webhook/mollie/payment",
////            "expiresAt"   => "2023-06-06T11:00:00+00:00",
//            "reusable"    => false,
//        ]);
//
//        dd($paymentLink);

		$request = Http::withHeaders($headers)
            ->post('https://api.mollie.com/v2/payment-links', [
                'amount'      => [
                    'value'    => "$totalPrice.00",
                    'currency' => $currency,
                ],
                'description' => "Payment for products",
                'redirectUrl' => $url,
				'reusable'    => false,
				'webhookUrl'  => "$host/api/v1/webhook/mollie/payment?$key=$modelId&lang=$this->language",
			]);

        $response = $request->json();

        if (!in_array($request->status(), [200, 201])) {
            $message = data_get($response, 'title') . ': ' . data_get($response, 'detail');
            throw new Exception($message, $request->status());
        }

        return PaymentProcess::updateOrCreate([
            'user_id'    => auth('sanctum')->id(),
			'model_id'   => $modelId,
			'model_type' => data_get($before, 'model_type'),
        ], [
            'id' => data_get($response, 'id'),
            'data' => [
                'url'        => data_get($response, '_links.paymentLink'),
                'price'      => $totalPrice,
				'cart'	     => $data,
				'payment_id' => $payment->id,
            ]
        ]);

    }

	/**
	 * @param array $data
	 * @return array
	 * @throws Throwable
	 */
	public function splitTransaction(array $data): array
	{
		return DB::transaction(function () use ($data) {
			$payment = Payment::where('tag', Payment::TAG_PAY_STACK)->first();

			$paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
			$payload        = $paymentPayload?->payload;
			$host 			= request()->getSchemeAndHttpHost();
			$token 		   	= base64_encode(data_get($payload, 'secret_key'));

			$headers = [
				'Authorization' => "Basic $token"
			];

			[$key, $before] = $this->getPayload($data, $payload);
			$modelId 		= data_get($before, 'model_id');

			$result = [];
			$split  = $data['split'] ?? 1;

			$totalPrice = round((float)$before['total_price'] * 100 / $split, 2);
			$totalPrice = round($totalPrice, 2);
			$url 		= "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&$key=$modelId";
			$trxRef		= "$modelId-" . time();

			$before['total_price'] = $totalPrice;

			if ($before['total_price'] <= 0) {
				throw new Exception('The minimum amount must be greater than 1' . $before['currency']);
			}

			$currencies = [
				'BRL' => 'BRL',
				'MXN' => 'MXN',
				'MYR' => 'MYR',
				'THB' => 'THB',
				'TWD' => 'TWD',
				'EUR' => 'EUR',
			];

			$currency = Str::upper(data_get($before, 'currency'));

			if (!in_array($currency, $currencies)) {
				throw new Exception("Support only: BRL, MXN, MYR, THB, TWD, EUR");
			}

			for ($i = 0; $split > $i; $i++) {

				$request = Http::withHeaders($headers)
					->post('https://api.mollie.com/v2/payment-links', [
						'amount'      => [
							'value'     => "$totalPrice.00",
							'currency' => $currency,
						],
						'description' => "Payment for products",
						'redirectUrl' => $url,
						'webhookUrl'  => "$host/api/v1/webhook/mollie/payment?$key=$modelId&lang=$this->language",
					]);

				$response = $request->json();

				if (!in_array($request->status(), [200, 201])) {
					$message = data_get($response, 'title') . ': ' . data_get($response, 'detail');
					throw new Exception($message, $request->status());
				}

				$paymentProcess =  PaymentProcess::updateOrCreate([
					'user_id'    => auth('sanctum')->id(),
					'model_id'   => $modelId,
					'model_type' => data_get($before, 'model_type'),
				], [
					'id' => data_get($response, 'id'),
					'data' => [
						'url'        => data_get($response, '_links.paymentLink'),
						'price'      => $totalPrice,
						'cart'	     => $data,
						'payment_id' => $payment->id,
					]
				]);

				$paymentProcess->id = $trxRef;

				$result[] = $paymentProcess;

			}

			return $result;
		});
	}

    /**
     * @param array $data
     * @param $shop
     * @param $currency
     * @return PaymentProcess|Model
     * @throws Exception
     */
    public function subscriptionProcessTransaction(array $data, $shop, $currency): Model|PaymentProcess
    {
        $payment        = Payment::where('tag', Payment::TAG_MOLLIE)->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $host = request()->getSchemeAndHttpHost();

        $token = base64_encode(data_get($payload, 'secret_key'));

        $headers = [
            'Authorization' => "Basic $token"
        ];

        $subscription = Subscription::find(data_get($data, 'subscription_id'));
        $totalPrice   = ceil($subscription->price * 2 * 100) / 2;
		$currencies   = [
			'BRL' => 'BRL',
			'MXN' => 'MXN',
			'MYR' => 'MYR',
			'THB' => 'THB',
			'TWD' => 'TWD',
			'EUR' => 'EUR',
		];

		if (!in_array($currency, $currencies)) {
			throw new Exception("Support only: BRL, MXN, MYR, THB, TWD, EUR");
		}

        $request = Http::withHeaders($headers)
            ->post('https://api.mollie.com/v2/payment-links', [
                'amount'      => [
                    'value'     => "$totalPrice.00",
                    'currency' => $currency,
                ],
                'description' => "Payment for products",
                'redirectUrl' => "$host/payment-success?subscription_id=$subscription->id&lang=$this->language",
                'webhookUrl'  => "$host/api/v1/webhook/mollie/payment?subscription_id=$subscription->id&lang=$this->language",
            ]);

        $response = $request->json();

        if (!in_array($request->status(), [200, 201])) {
            $message = data_get($response, 'title') . ': ' . data_get($response, 'detail');
            throw new Exception($message, $request->status());
        }

        return PaymentProcess::updateOrCreate([
            'user_id'    => auth('sanctum')->id(),
            'model_id'   => $subscription->id,
            'model_type' => Subscription::class,
        ], [
            'id' => data_get($response, 'id'),
            'data' => [
                'url'      		  => data_get($response, '_links.paymentLink'),
                'price'    		  => $subscription->price,
                'subscription_id' => $subscription->id,
                'shop_id'  		  => $shop->id,
				'payment_id' 	  => $payment->id,
            ]
        ]);

    }

}
