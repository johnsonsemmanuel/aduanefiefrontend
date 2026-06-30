<?php

namespace App\Services\PaymentService;

use DB;
use Str;
use Http;
use Exception;
use Throwable;
use App\Models\Payout;
use App\Models\Payment;
use App\Models\Subscription;
use App\Helpers\ResponseError;
use App\Models\PaymentPayload;
use App\Models\PaymentProcess;
use Illuminate\Database\Eloquent\Model;

class MoyasarService extends BaseService
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
        $payment = Payment::where('tag', Payment::TAG_MOYA_SAR)->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $host = request()->getSchemeAndHttpHost();

        [$key, $before] = $this->getPayload($data, $payload);
        $modelId 		= data_get($before, 'model_id');

        $totalPrice = round((float)data_get($before, 'total_price') * 100, 2);

        $url  = "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&$key=$modelId";

        $request = Http::withBasicAuth(data_get($payload, 'secret_key'), '')
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post('https://api.moyasar.com/v1/invoices', [
                'amount'      => $totalPrice,
                'currency'    => Str::lower($order->currency?->title ?? data_get($payload, 'currency')),
                'description' => 'Payment for products',
                'back_url'    => $url,
                'success_url' => $url,
            ]);

        $response = $request->json();

        if (!in_array($request->status(), [200, 201])) {
            throw new Exception($request->json('message', 'error in moya-sar'));
        }

        return PaymentProcess::updateOrCreate([
            'user_id'    => auth('sanctum')->id(),
            'model_id'   => $modelId,
            'model_type' => data_get($before, 'model_type'),
        ], [
            'id' => data_get($response, 'id'),
            'data' => array_merge([
                'url'   	 => $response['url'],
                'price' 	 => $totalPrice,
                'cart'  	 => $data,
                'payment_id' => $payment->id,
            ], $before),
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
            $split  		= $data['split'] ?? 1;

            [$key, $before] = $this->getPayload($data, $payload);

            $result 	= [];
            $modelId 	= data_get($before, 'model_id');
            $totalPrice = round((float)$before['total_price'] * 100 / $split, 2);
            $totalPrice = round($totalPrice, 2);
            $url 		= "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&$key=$modelId";
            $trxRef		= "$modelId-" . time();
            $currency 	= Str::upper(data_get($before, 'currency'));

            $before['total_price'] = $totalPrice;

            if ($before['total_price'] <= 0) {
                throw new Exception('The minimum amount must be greater than 1' . $before['currency']);
            }

            if(!in_array($currency, ['AED','EGP','SAR','OMR','JOD','US'])) {
                throw new Exception(__('errors.' . ResponseError::CURRENCY_NOT_FOUND, locale: $this->language));
            }

            for ($i = 0; $split > $i; $i++) {

                $request = Http::withBasicAuth(data_get($payload, 'secret_key'), '')
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ])
                    ->post('https://api.moyasar.com/v1/invoices', [
                        'amount'      => $totalPrice,
                        'currency'    => Str::lower($order->currency?->title ?? data_get($payload, 'currency')),
                        'description' => 'Payment for products',
                        'back_url'    => $url,
                        'success_url' => $url,
                    ]);

                $response = $request->json();

                if (!in_array($request->status(), [200, 201])) {
                    throw new Exception($request->json('message', 'error in moya-sar'));
                }

                $paymentProcess = PaymentProcess::updateOrCreate([
                    'user_id'    => auth('sanctum')->id(),
                    'model_id'   => $modelId,
                    'model_type' => data_get($before, 'model_type'),
                ], [
                    'id' => data_get($response, 'id'),
                    'data' => [
                        'url'   	 => $response['url'],
                        'price' 	 => $totalPrice,
                        'cart'  	 => $data,
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
        $payment = Payment::where('tag', Payment::TAG_MOYA_SAR)->first();

        $paymentPayload = PaymentPayload::where('payment_id', $payment?->id)->first();
        $payload        = $paymentPayload?->payload;

        $host = request()->getSchemeAndHttpHost();

        $subscription   = Subscription::find(data_get($data, 'subscription_id'));

        $request = Http::withBasicAuth(data_get($payload, 'secret_key'), '')
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post('https://api.moyasar.com/v1/invoices', [
                'amount'      => $subscription->price * 100,
                'currency'    => Str::lower(data_get($paymentPayload?->payload, 'currency', $currency)),
                'description' => "Payment for products",
                'back_url'    => "$host/payment-success?subscription_id=$subscription->id&lang=$this->language",
                'success_url' => "$host/payment-success?subscription_id=$subscription->id&lang=$this->language",
            ]);

        $response = $request->json();

        if (!in_array($request->status(), [200, 201])) {
            throw new Exception($request->json('message', 'error in moya-sar'));
        }

        return PaymentProcess::updateOrCreate([
            'user_id'    => auth('sanctum')->id(),
            'model_id'   => $subscription->id,
            'model_type' => Subscription::class,
        ], [
            'id' => data_get($response, 'id'),
            'data' => [
                'url'      		  => $response['url'],
                'price'    		  => $subscription->price,
                'subscription_id' => $subscription->id,
                'shop_id'  		  => $shop->id,
                'payment_id' 	  => $payment->id,
            ]
        ]);
    }

}
