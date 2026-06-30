<?php

namespace App\Providers;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Helpers\ResponseError;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    use ApiResponse;
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(5000)->by(optional($request->user())->id ?: $request->ip())
                ->response(function () use ($request) {
                    $ips = collect(Cache::get('block-ips'));
                    Cache::set('block-ips', $ips->merge([$request->ip()]), 86600000000);
                    return $this->errorResponse(ResponseError::ERROR_429, __('errors.' . ResponseError::ERROR_429, locale: request('lang', 'en')));
                });
        });
    }
}
