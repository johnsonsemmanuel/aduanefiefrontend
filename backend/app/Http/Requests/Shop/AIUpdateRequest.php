<?php
declare(strict_types=1);

namespace App\Http\Requests\Shop;

use App\Http\Requests\BaseRequest;

class AIUpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'ai_token_limit' => 'int|max:2147483647',
            'ai_access'      => 'bool',
        ];
    }
}
