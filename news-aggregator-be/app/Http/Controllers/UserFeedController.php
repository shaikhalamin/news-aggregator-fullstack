<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserFeedRequest;
use App\Http\Requests\UpdateUserFeedRequest;
use App\Models\UserFeed;
use App\Services\UserFeed\UserFeedService;
use Symfony\Component\HttpFoundation\Response as RESPONSE;

class UserFeedController extends AbstractApiController
{

    public function __construct(private UserFeedService $userFeedService)
    {
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        $response = $this->userFeedService->list($userId);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserFeedRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserFeed $userFeed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserFeed $userFeed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserFeedRequest $request, UserFeed $userFeed)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserFeed $userFeed)
    {
        //
    }
}
