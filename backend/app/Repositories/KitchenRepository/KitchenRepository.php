<?php

namespace App\Repositories\KitchenRepository;

use App\Models\Kitchen;
use App\Models\Language;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class KitchenRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Kitchen::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter): LengthAwarePaginator
    {
        /** @var Kitchen $model */
        $model = $this->model();

        return $model
            ->filter($filter)
            ->with([
                'translations',
                'translation' => fn($q) => $q->where('locale', $this->language),
                'shop.translation' => fn($q) => $q->where('locale', $this->language),
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param Kitchen $model
     * @return Kitchen|null
     */
    public function show(Kitchen $model): Kitchen|null
    {
        return $model->loadMissing([
            'translations',
            'translation' => fn($q) => $q->where('locale', $this->language),
            'shop.translation' => fn($q) => $q->where('locale', $this->language),
        ]);
    }
}
