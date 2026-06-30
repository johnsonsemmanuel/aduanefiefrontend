<?php
declare(strict_types=1);

namespace App\Services\PaymentService;

use App\Models\Payment;
use App\Models\PaymentProcess;
use App\Models\Payout;
use Exception;
use Firebase\JWT\JWT;
use Http;

class ZainCashService extends BaseService
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    /**
     * @param array $data
     * @return PaymentProcess
     * @throws Exception
     */
    public function orderProcessTransaction(array $data): PaymentProcess
    {
        /** @var Payment $payment */
        $payment = Payment::with(['paymentPayload'])->where('tag', Payment::TAG_ZAIN_CASH)->first();

        $payload = $payment?->paymentPayload?->payload ?? [];

		[$key, $before] = $this->getPayload($data, $payload);

		$modelId 	= data_get($before, 'model_id');
		$totalPrice = round((float)data_get($before, 'total_price') * 100, 2);
		$host 		= request()->getSchemeAndHttpHost();
		$url  		= "$host/order-stripe-success?token={CHECKOUT_SESSION_ID}&$key=$modelId";
        $time 		= time();

        $data = [
            'amount'      => $totalPrice,
            'serviceType' => 'Order',
            'msisdn'      => $payload['msisdn'],
            'orderId'     => $modelId,
            'redirectUrl' => "$host/payment-success?$key=$modelId&lang=$this->language",
            'iat'         => $time,
            'exp'         => $time + 60 * 60 * 4
        ];

        $newToken = JWT::encode($data, $payload['key'],'HS256');

        $init = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])
            ->post(($payload['url'] ?? 'https://test.zaincash.iq') . '/transaction/init', [
                'token'      => $newToken,
                'merchantId' => $payload['merchantId'],
                'lang'       => $this->language
            ]);

        $errorMessage = $init->json('err.msg');

        if (!empty($errorMessage)) {
            throw new Exception($errorMessage, $init->status());
        }

        $init = $init->json();

        $transactionId = $init['id'];

        $newUrl = ($payload['url'] ?? 'https://test.zaincash.iq') . "/transaction/pay?id=$transactionId";

        return PaymentProcess::updateOrCreate([
            'user_id'    => auth('sanctum')->id(),
			'model_id'   => $modelId,
			'model_type' => data_get($before, 'model_type'),
        ], [
            'id'   => $transactionId,
            'data' => [
				'url' 		 => $newUrl,
				'payment_id' => $payment?->id,
				'price' 	 => $totalPrice,
			]
        ]);
    }

}
