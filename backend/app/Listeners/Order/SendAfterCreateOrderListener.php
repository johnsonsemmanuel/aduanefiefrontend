<?php

namespace App\Listeners\Order;

use App\Events\Order\SendAfterCreateOrder;
use App\Helpers\NotificationHelper;
use App\Models\Order;
use App\Models\Settings;
use App\Traits\Loggable;
use App\Traits\Notification;
use Exception;

class SendAfterCreateOrderListener
{
    use Loggable, Notification;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event
     * @param SendAfterCreateOrder $event
     * @return void
     */
    public function handle(SendAfterCreateOrder $event): void
    {
        try {
            $this->newOrderNotification($event->order, $event->language);

            if ((int)data_get(Settings::where('key', 'order_auto_approved')->first(), 'value') === 1) {
                (new NotificationHelper)->autoAcceptNotification(
                    $event->order,
                    $event->language,
                    Order::STATUS_ACCEPTED
                );
            }
        } catch (Exception $e) {
            $this->error($e);
        }
    }
}
