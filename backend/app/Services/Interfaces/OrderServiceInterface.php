<?php

namespace App\Services\Interfaces;

interface OrderServiceInterface
{
    public const ORDER_STATUS_NEW = 1;
    
    public function create(array $data);

    public function update(int $id, array $data);

    public function updateDeliveryMan(?int $orderId, int $deliveryman, ?int $shopId);

    public function destroy(?array $ids = []);
}
