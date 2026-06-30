<?php

namespace App\Jobs;

use Cache;
use Exception;
use App\Models\User;
use App\Models\Order;
use App\Traits\Loggable;
use App\Models\Settings;
use App\Models\Translation;
use App\Traits\Notification;
use Illuminate\Bus\Queueable;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};

class AttachDeliveryMan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Loggable, Notification;

    public ?Order $order;
    public ?string $language;
    public int $index;
    public array $users;

    public function __construct(?Order $order, ?string $language, array $users = [], int $index = 0)
    {
        $this->order    = $order;
        $this->language = $language;
        $this->index    = $index;
        $this->users    = $users;

        if (!isset($users[0])) {
            $this->users = Cache::remember('delivery_man_users', 900, function () {
                return User::with('deliveryManSetting')
                    ->whereHas('deliveryManSetting', fn($q) => $q->where('online', 1))
                    ->whereNotNull('firebase_token')
                    ->select(['firebase_token', 'id'])
                    ->get()
                    ->map(fn($u) => [
                        'id' => $u->id,
                        'firebase_token' => $u->firebase_token,
                    ])
                    ->values()
                    ->toArray();
            });
        }
    }

    public function handle(): void
    {
        try {
            $order = $this->order;

            if (empty($order) || $order->delivery_type !== Order::DELIVERY) return;

            if ($order->deliveryman) return;

            $second = Settings::where('key', 'deliveryman_order_acceptance_time')->first();
            $delaySeconds = data_get($second, 'value', 30);

            if (empty($this->users)) return;

            $user = $this->users[$this->index] ?? null;

            if (!$user) return;

            if (!$user['firebase_token']) {
                AttachDeliveryMan::dispatch($order, $this->language, $this->users, $this->index + 1);
                return;
            }

            $needAttachDeliveryMan = Translation::where('key', 'need_attach_deliveryman')
                ->where('locale', $this->language)
                ->first()?->value ?? 'need attach deliveryman';

            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId()}/messages:send";
            $token = $this->updateToken();

            $headers = [
                'Authorization' => "Bearer $token",
                'Content-Type'  => 'application/json'
            ];

            $oldTokensCount = count($user['firebase_token']);
            $tokens = $user['firebase_token'];

            foreach ($tokens as $key => $receiver) {
                $r = Http::withHeaders($headers)->post($url, [
                    'message' => [
                        'token' => $receiver,
                        'notification' => [
                            'title' => "New order #{$order->id}",
                            'body'  => $needAttachDeliveryMan,
                        ],
                        'data'  => (new NotificationHelper)->deliveryManOrder($order),
                        'android' => [
                            'notification' => ['sound' => 'default']
                        ],
                        'apns' => [
                            'payload' => ['aps' => ['sound' => 'default']]
                        ]
                    ],
                ]);

                if ($r->status() !== 200) {
                    unset($tokens[$key]);
                }

            }

            if ($oldTokensCount !== count($tokens)) {
                User::where('id', $user['id'])->update(['firebase_token' => (array)$tokens]);
            }

            AttachDeliveryMan::dispatch($order, $this->language, $this->users, $this->index + 1)
                ->delay(now()->addSeconds($delaySeconds));

        } catch (Exception $e) {
            $this->error($e);
        }
    }
}
