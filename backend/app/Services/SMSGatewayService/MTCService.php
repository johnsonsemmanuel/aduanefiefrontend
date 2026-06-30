<?php

namespace App\Services\SMSGatewayService;

use Exception;
use App\Models\SmsPayload;
use App\Services\CoreService;
use Illuminate\Support\Facades\Http;

class MTCService extends CoreService
{
    protected function getModelClass(): string
    {
        return SmsPayload::class;
    }

    /**
     * @param $phone
     * @param $otp
     * @param SmsPayload $smsPayload
     * @return array|bool[]
     */
    public function sendSms($phone, $otp, SmsPayload $smsPayload): array
    {
        try {
            $otpCode     = data_get($otp, 'otpCode');
            $apiKey      = data_get($smsPayload->payload, 'api_key', '');
            $phoneNumber = data_get($smsPayload->payload, 'phone_number', '');
            $text        = data_get($smsPayload->payload, 'text', 'Confirmation code :code');

            if (strlen($phone) < 7) {
                throw new Exception('Invalid phone number', 400);
            }

            $request = Http::withHeaders([
                'Authorization' => "Bearer $apiKey"
            ])
                ->post('https://api.exolve.ru/messaging/v1/SendSMS', [
                    'number'      => $phoneNumber,
                    'destination' => $phone,
                    'text'        => str_replace(':code', $otpCode, $text),
                ]);

            if (!in_array($request->status(), [200, 201])) {
                throw new Exception($request->json('error.message', 'error send otp'));
            }

            return ['status' => true, 'message' => 'success'];

        } catch (Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

}
