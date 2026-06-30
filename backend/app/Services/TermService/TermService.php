<?php

namespace App\Services\TermService;

use DB;
use Throwable;
use App\Models\TermCondition;
use App\Services\CoreService;
use App\Helpers\ResponseError;
use App\Traits\SetTranslations;

class TermService extends CoreService
{
    use SetTranslations;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return TermCondition::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $term = DB::transaction(function () use ($data) {
                $term = $this->model()->create(['type' => $data['type'] ?? TermCondition::TYPE_USER]);

                $this->setTranslations($term, $data);

                return $term;
            });

            return ['status' => true, 'code' => ResponseError::NO_ERROR, 'data' => $term];
        } catch (Throwable $e) {
            $this->error($e);

            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => $e->getMessage(),
            ];
        }
    }

}
