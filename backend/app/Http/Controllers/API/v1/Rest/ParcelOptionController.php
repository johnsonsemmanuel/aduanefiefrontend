<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Models\ParcelOption;
use App\Helpers\ResponseError;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\ParcelOptionResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Repositories\ParcelOptionRepository\ParcelOptionRepository;

class ParcelOptionController extends RestBaseController
{
    public function __construct(private ParcelOptionRepository $repository)
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $orders = $this->repository->paginate($request->all());

        return ParcelOptionResource::collection($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param ParcelOption $parcelOption
     * @return JsonResponse
     */
    public function show(ParcelOption $parcelOption): JsonResponse
    {

        if (!Cache::get('tvoirifgjn.seirvjrc') || data_get(Cache::get('tvoirifgjn.seirvjrc'), 'active') != 1) {
            abort(403);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            ParcelOptionResource::make($this->repository->show($parcelOption))
        );
    }

}
