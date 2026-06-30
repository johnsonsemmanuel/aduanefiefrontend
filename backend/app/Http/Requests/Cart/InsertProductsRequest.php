<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class InsertProductsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'shop_id'               => [
                'required',
                'integer',
                Rule::exists('shops', 'id')->whereNull('deleted_at')
            ],
            'currency_id'           => [
                'required',
                'integer',
                Rule::exists('currencies', 'id')->whereNull('deleted_at')
            ],
            'products'              => 'required|array',
            'products.*.stock_id'   =>  [
                'integer',
                Rule::exists('stocks', 'id')->whereNull('deleted_at')
            ],
            'products.*.quantity'   => 'required|numeric',
            'products.*.parent_id'  => [
                'nullable',
                'integer',
                Rule::exists('stocks', 'id')->where('addon', 0)->whereNull('deleted_at')
            ],
            'products.*.custom_name'    => 'string|max:255',
            'products.*.custom_unit_id' => 'exists:units,id',
            'products.*.custom_photo'   => 'string|max:255',
            'products.*.custom_price'   => 'numeric|max:2147483647',
            'products.*.custom_id'      => [
                'int',
                Rule::exists('cart_details', 'id')->whereNull('stock_id')
            ],
            'custom_products_delete_ids'   => 'array',
            'custom_products_delete_ids.*' => 'exists:cart_details,id',
        ];
    }
}
