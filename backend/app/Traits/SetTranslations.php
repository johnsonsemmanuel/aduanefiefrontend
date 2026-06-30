<?php

namespace App\Traits;

use Str;
use Throwable;
use App\Models\Shop;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Database\Eloquent\Model;

trait SetTranslations
{
    /**
     * @param Model $model Все модели у которых есть таблица $model_translations
     * @param array $data
     * @param bool $hasDesc
     * @param bool $hasAddress
     * @return void
     */
    public function setTranslations(Model $model, array $data, bool $hasDesc = true, bool $hasAddress = false): void
    {
        try {

            /** @var Category $model */
			if (is_array(data_get($data, 'title'))) {
                $model->translations()->forceDelete();
            }

			$defaultLocale = Language::whereDefault(1)->first()?->locale;

			$title = (array)data_get($data, 'title');

			try {
				$this->setSlug($model, $title, $defaultLocale);
			} catch (Throwable) {}

			foreach ($title as $index => $value) {

                $model->translations()->create([
                    'title'         => $value,
                    'locale'        => $index,
                    'description'   => @$data['description'][$index]  ?? '',
                    'address'       => @$data['address'][$index]      ?? '',
                    'button_text'   => @$data['button_text'][$index]  ?? '',
                    'term'          => @$data['term'][$index]         ?? '',
                    'sub_title'     => @$data['sub_title'][$index]    ?? '',
                    'price_title'   => @$data['price_title'][$index]  ?? '',
                ]);

            }

        } catch (Throwable $e) {
            $this->error($e);
        }
    }

	/**
	 * Генерируем slug для определенных моделей заданных в переменной $classes внутри функции
	 * @param Model $model
	 * @param array $title
	 * @param string $defaultLocale
	 * @return void
	 */
	public function setSlug(Model $model, array $title, string $defaultLocale): void
	{
		$classes = [
			Shop::class     => Shop::class,
			Category::class => Category::class,
			Brand::class    => Brand::class,
			Product::class  => Product::class,
		];

		if (in_array(get_class($model), $classes)) {

			/** и другие классы в переменной $classes @var Shop $model */
			$model->update([
				'slug' => Str::slug($title[$defaultLocale], language: $defaultLocale) . "-$model->id"
			]);

		}
	}

}
