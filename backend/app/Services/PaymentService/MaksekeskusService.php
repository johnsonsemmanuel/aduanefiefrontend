<?php

namespace App\Services\PaymentService;

use App\Models\Payment;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use App\Models\Payout;
use App\Models\Shop;
use App\Models\Subscription;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Maksekeskus\Maksekeskus;
use Str;
use Throwable;

class MaksekeskusService extends BaseService
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
        $payment        = Payment::where('tag', Payment::TAG_MAKSEKESKUS)->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload->payload;
        $host           = request()->getSchemeAndHttpHost();

		[$key, $before] = $this->getPayload($data, $payload);
		$modelId 		= data_get($before, 'model_id');

		$totalPrice = round((float)data_get($before, 'total_price') * 100, 2);

		$shopId         = $payload['shop_id'];
        $keyPublishable = $payload['key_publishable'];
        $keySecret      = $payload['key_secret'];

        $MK = new Maksekeskus($shopId, $keyPublishable, $keySecret, (bool)$payload['demo']);

		$user = auth('sanctum')->user();

		$url  = "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&$key=$modelId";

		$body = [
            'transaction' => [
                'amount'        => $totalPrice,
                'currency'      => Str::lower(data_get($before, 'currency')),
                'id'            => "id: #$modelId",
                'reference'     => "reference: #$modelId",
                'merchant_data' => "merchant: #$modelId",
            ],
            'customer' => [
                'email'   => $user?->email ?? Str::random(8) . '@gmail.com',
                'ip'      => request()->ip(),
                'country' => $payload['country'],
                'locale'  => $payload['country'],
            ],
            'app_info' => [
                'module'            => 'E-Commerce',
                'module_version'    => '1.0.1',
                'platform'          => 'Web',
                'platform_version'  => '2.0'
            ],
            'return_url' => "$url&lang=$this->language&status=success",
            'cancel_url' => "$url&lang=$this->language&status=canceled",
            'notification_url' => "$host/api/v1/webhook/maksekeskus/payment",
            'transaction_url' => [
                'return_url' => [
                    'url'    => "$url&lang=$this->language&status=success",
                    'method' => 'POST',
                ],
                'cancel_url' => [
                    'url'    => "$url&lang=$this->language&status=canceled",
                    'method' => 'POST',
                ],
                'notification_url' => [
                    'url'    => "$host/api/v1/webhook/maksekeskus/payment",
                    'method' => 'POST',
                ],
            ],
        ];

        try {
            $data = $MK->createTransaction($body);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return PaymentProcess::updateOrCreate([
            'user_id'    => auth('sanctum')->id(),
			'model_id'   => $modelId,
			'model_type' => data_get($before, 'model_type'),
        ], [
            'id' => data_get($data, 'id'),
            'data' => [
                'methods'    => data_get($data, 'payment_methods.banklinks'),
                'payment_id' => $payment->id,
                'price'      => $totalPrice,
				'cart'		 => $data,
            ]
        ]);
    }

    /**
     * @param array $data
     * @param Shop $shop
     * @param $currency
     * @return PaymentProcess|Model
     * @throws Exception
     */
    public function subscriptionProcessTransaction(array $data, Shop $shop, $currency): Model|PaymentProcess
    {
        $payment        = Payment::where('tag', Payment::TAG_MAKSEKESKUS)->first();
        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload->payload;
        $host           = request()->getSchemeAndHttpHost();

        $subscription   = Subscription::find(data_get($data, 'subscription_id'));

        $totalPrice     = round($subscription->price * 100, 1);

        $shopId         = $payload['shop_id'];
        $keyPublishable = $payload['key_publishable'];
        $keySecret      = $payload['key_secret'];

        $MK = new Maksekeskus($shopId, $keyPublishable, $keySecret, (bool)$payload['demo']);

		$user = auth('sanctum')->user();

		$url  = "$host/payment-success?subscription_id=$subscription->id";

        $body = [
            'transaction' => [
                'amount'        => $totalPrice,
                'currency'      => Str::lower(data_get($payload, 'currency', $currency)),
                'id'            => "id: #$subscription->id",
                'reference'     => "reference: #$subscription->id",
                'merchant_data' => "merchant: #$subscription->id",
            ],
            'customer' => [
                'email'   => $user?->email ?? Str::random(8) . '@gmail.com',
                'ip'      => request()->ip(),
                'country' => $payload['country'],
                'locale'  => $payload['country'],
            ],
            'app_info' => [
                'module'            => 'E-Commerce',
                'module_version'    => '1.0.1',
                'platform'          => 'Web',
                'platform_version'  => '2.0'
            ],
            'return_url' => "$url&lang=$this->language&status=success",
            'cancel_url' => "$url&lang=$this->language&status=canceled",
            'notification_url' => "$host/api/v1/webhook/maksekeskus/payment",
            'transaction_url' => [
                'return_url' => [
                    'url'    => "$url&lang=$this->language&status=success",
                    'method' => 'POST',
                ],
                'cancel_url' => [
                    'url'    => "$url&lang=$this->language&status=canceled",
                    'method' => 'POST',
                ],
                'notification_url' => [
                    'url'    => "$host/api/v1/webhook/maksekeskus/payment",
                    'method' => 'POST',
                ],
            ],
        ];

        try {
            $data = $MK->createTransaction($body);
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }

        return PaymentProcess::updateOrCreate([
            'user_id'    => auth('sanctum')->id(),
            'model_id'   => $subscription->id,
            'model_type' => get_class($subscription),
        ], [
            'id' => data_get($data, 'id'),
            'data' => [
                'methods'    	  => data_get($data, 'payment_methods.banklinks'),
                'payment_id' 	  => $payment?->id,
                'price'      	  => $totalPrice,
                'shop_id'    	  => $shop->id,
                'subscription_id' => $subscription->id,
            ]
        ]);
    }

}
