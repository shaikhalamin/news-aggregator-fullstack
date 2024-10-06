<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchFilterRequest;
use App\Services\SearchFilter\SearchFilterService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as RESPONSE;

class SearchFilterController extends AbstractApiController
{
    public function __construct(private SearchFilterService $searchFilterService)
    {
    }

    public function getSourceCategories(string $source)
    {
        $response = $this->searchFilterService->getCategoriesBySource($source);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }

    public function search(SearchFilterRequest $request)
    {
        $response = $this->searchFilterService->filterSearch($request->validated());
        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }
}
