<?php

namespace App\Http\Requests\Cart;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseRequest;

class GroupStoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop_id'           => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'stock_id'          =>  [
                'nullable',
                'integer',
                Rule::exists('stocks', 'id')->whereNull('deleted_at')
            ],
            'custom_name'    => 'string|max:255',
            'custom_unit_id' => 'exists:units,id',
            'custom_photo'   => 'string|max:255',
            'custom_price'   => 'numeric|max:2147483647',
            'custom_id'      => [
                'int',
                Rule::exists('cart_details', 'id')->whereNull('stock_id')
            ],
            'custom_products_delete_ids'   => 'array',
            'custom_products_delete_ids.*' => 'exists:cart_details,id',

            'quantity'          => 'required|numeric|min:1',
            'cart_id'           => 'required|integer|exists:carts,id',
            'user_cart_uuid'    => 'required|string|exists:user_carts,uuid',
            'name'              => 'string',
        ];
    }

}
