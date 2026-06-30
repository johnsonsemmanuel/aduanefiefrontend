<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UnitResource;
use App\Http\Requests\FilterParamsRequest;
use App\Repositories\UnitRepository\UnitRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitController extends RestBaseController
{
    /**
     * @param UnitRepository $unitRepository
     */
    public function __construct(private UnitRepository $unitRepository)
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function paginate(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $units = $this->unitRepository->unitsPaginate($request->all());

        return UnitResource::collection($units);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $unit = $this->unitRepository->unitDetails($id);

        if (empty($unit)) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            UnitResource::make($unit)
        );
    }
}
