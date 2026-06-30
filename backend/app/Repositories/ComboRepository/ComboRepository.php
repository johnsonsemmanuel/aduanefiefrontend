<?php
declare(strict_types=1);

namespace App\Repositories\ComboRepository;

use Schema;
use App\Models\Combo;
use App\Models\Language;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ComboRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Combo::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter = []): LengthAwarePaginator
    {
		$column = data_get($filter, 'column','id');

        if ($column !== 'id') {
            $column = Schema::hasColumn('combos', $column) ? $column : 'id';
        }

        return Combo::filter($filter)
			->with([
			'shop.translation' => fn($q) => $q
				->select('id', 'shop_id', 'locale', 'title')
				->where('locale', $this->language)
        ])
            ->orderBy($column, $filter['sort'] ?? 'desc')
            ->paginate($filter['perPage'] ?? 10);
    }

	/**
	 * @param Combo $combo
	 * @return Combo
	 */
    public function show(Combo $combo): Combo
    {
		return $combo->loadMissing([
			'shop.translation' => fn($q) => $q
				->select('id', 'shop_id', 'locale', 'title')
				->where('locale', $this->language),
			'stocks.product:id,uuid,slug',
			'stocks.product.translation' => fn($q) => $q
				->select('id', 'shop_id', 'locale', 'title')
				->where('locale', $this->language),
		]);
    }
}
