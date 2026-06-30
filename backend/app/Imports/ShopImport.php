<?php

namespace App\Imports;

use DB;
use Throwable;
use App\Models\Shop;
use App\Traits\Loggable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ShopImport implements ToCollection, WithHeadingRow, WithBatchInserts
{
    use Importable, Loggable;

    /**
     * @param Collection $collection
     * @return void
     */
    public function collection(Collection $collection): void
    {

        foreach ($collection as $row) {

            $explodeDeliveryTime = explode(',', data_get($row, 'delivery_time', ''));

            $deliveryTime = [
                'from'  => str_replace(['from:', ' '], '', data_get($explodeDeliveryTime, 0, '')),
                'to'    => str_replace(['to:', ' '], '',   data_get($explodeDeliveryTime, 1, '')),
                'type'  => str_replace(['type:', ' '], '', data_get($explodeDeliveryTime, 2, '')),
            ];

            $location = explode(',', data_get($row, 'location', ''));

            $shop = Shop::updateOrCreate(['user_id' => data_get($row, 'user_id')], [
                'tax'               => data_get($row, 'tax', 0),
                'percentage'        => data_get($row, 'percentage', 0),
                'location'          => [
                    'latitude'  => data_get($location, 0),
                    'longitude' => data_get($location, 1),
                ],
                'phone'             => data_get($row, 'phone'),
                'show_type'         => data_get($row, 'show_type', 0),
                'open'              => data_get($row, 'open', 0) !== null ? data_get($row, 'open', 0) : 0,
                'background_img'    => data_get($row, 'background_img', ''),
                'logo_img'          => data_get($row, 'logo_img', ''),
                'min_amount'        => data_get($row, 'min_amount', 1),
                'status'            => data_get($row, 'status', 'new'),
                'status_note'       => data_get($row, 'status_note', ''),
                'take'              => data_get($row, 'take') !== null ? data_get($row, 'take') : '',
                'delivery_time'     => $deliveryTime,
                'price'             => data_get($row, 'price', 0),
                'price_per_km'      => data_get($row, 'price_per_km', 0),
            ]);

            $this->downloadImages(Shop::find($shop->id), data_get($row, 'img_urls', ''));
        }

    }

    private function downloadImages(Shop $shop, ?string $imgUrls): bool
    {
        try {

            if (empty($imgUrls)) {
                return false;
            }

            $urls = explode(',', $imgUrls);

            foreach ($urls as $key => $url) {

                if (!str_contains($url, 'http')) {
                    unset($urls[$key]);
                }

            }

            DB::transaction(function () use ($shop, $urls) {
                $shop->galleries()->delete();

                $shop->update(['logo_img' => data_get($urls, 0)]);
                $shop->update(['background_img' => data_get($urls, 1)]);

                $shop->uploads($urls);
            });

            return true;
        } catch (Throwable $e) {
            $this->error($e);
        }

        return false;
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 200;
    }

}
