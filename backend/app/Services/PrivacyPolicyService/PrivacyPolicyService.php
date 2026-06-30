<?php

namespace App\Services\PrivacyPolicyService;

use App\Models\TermCondition;
use Throwable;
use App\Services\CoreService;
use App\Models\PrivacyPolicy;
use App\Helpers\ResponseError;
use App\Traits\SetTranslations;

class PrivacyPolicyService extends CoreService
{
    use SetTranslations;

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return PrivacyPolicy::class;
    }

    /**
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        try {
            $exist = PrivacyPolicy::firstWhere('type', $data['type'] ?? TermCondition::TYPE_USER);

            $exist?->forceDelete();

            $privacyPolicy = $this->model()->create(['type' => $data['type'] ?? TermCondition::TYPE_USER]);

            $this->setTranslations($privacyPolicy, $data, true);

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $privacyPolicy->load(['translations'])
            ];
        } catch (Throwable $e) {

            $this->error($e);

            return [
                'status'  => false,
                'code'    => ResponseError::ERROR_501,
                'message' => __('errors.' . ResponseError::ERROR_501, locale: $this->language)
            ];
        }
    }
}
