<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchFilterRequest;
use App\Http\Requests\StoreUserFeedRequest;
use App\Http\Requests\UpdateUserFeedRequest;
use App\Models\UserFeed;
use App\Services\UserFeed\UserFeedService;
use Symfony\Component\HttpFoundation\Response as RESPONSE;

class UserFeedController extends AbstractApiController
{

    public function __construct(private UserFeedService $userFeedService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(SearchFilterRequest $request)
    {
        $userId = auth()->user()->id;
        $response = $this->userFeedService->feedFilterList($request->validated(), $userId);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }

}
