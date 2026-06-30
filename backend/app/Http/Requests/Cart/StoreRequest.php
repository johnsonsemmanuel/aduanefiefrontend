<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'cart_id'       => 'nullable|integer|exists:carts,id',
            'shop_id'       => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'stock_id'      => [
                'nullable',
                'integer',
                Rule::exists('stocks', 'id')->where('addon', 0)->whereNull('deleted_at')
            ],
            'quantity'      => 'required|numeric',
            'group'         => 'boolean',
            'currency_id'   => [
                'required',
                'integer',
                Rule::exists('currencies', 'id')->whereNull('deleted_at')
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
        ];
    }
}

