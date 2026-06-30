<?php

namespace App\Http\Resources\Cart;

use App\Http\Resources\UnitResource;
use App\Models\CartDetail;
use Illuminate\Http\Request;
use App\Http\Resources\StockResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var CartDetail|JsonResource $this */
        return [
            'id'                 => $this->id,
            'quantity'           => $this->quantity,
            'bonus'              => $this->bonus,
            'price'              => $this->rate_price,
            'discount'           => $this->rate_discount,
            'updated_at'         => $this->updated_at,
            'custom_name'        => $this->custom_name,
            'custom_unit_id'     => $this->custom_unit_id,
            'custom_photo'       => $this->custom_photo,
            'note'               => $this->when(request("notes.$this->id"), request("notes.$this->id")),
            'is_custom'          => (bool)$this->custom_name,
            'custom_by'          => $this->when($this->custom_name, $this->custom_unit_id ? 'amount' : 'quantity'),
            'stock'              => StockResource::make($this->whenLoaded('stock')),
            'parent'             => CartDetailResource::make($this->whenLoaded('parent')),
            'addons'             => CartDetailResource::collection($this->whenLoaded('children')),
            'custom_unit'        => UnitResource::make($this->whenLoaded('customUnit')),
        ];
    }
}
