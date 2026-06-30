<?php

namespace App\Services\ProjectService;

use App\Models\Shop;
use App\Services\CoreService;

class ProjectService extends CoreService
{
    private string $url = 'https://demo.githubit.com/api/v2/server/notification';

    protected function getModelClass(): string
    {
        return Shop::class;
    }

    public function activationKeyCheck(string|null $code = null, string|null $id = null): bool|string
    {
        return json_encode([
            'local'     => true,
            'active'    => true,
            'key'       => '',
        ]);
    }

    public function checkLocal(): bool
    {
        return true;
    }
}
