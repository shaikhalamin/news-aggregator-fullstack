<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserPreferenceRequest;
use App\Http\Requests\UpdateUserPreferenceRequest;
use App\Jobs\FetchUserFeedJob;
use App\Models\UserPreference;
use App\Services\Preference\UserPreferenceService;
use Symfony\Component\HttpFoundation\Response as RESPONSE;

class UserPreferenceController extends AbstractApiController
{

    public function __construct(private UserPreferenceService $userPreferenceService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = auth()->user()->id;
        $response = $this->userPreferenceService->list($userId);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserPreferenceRequest $request)
    {
        $userId = auth()->user()->id;
        $response = $this->userPreferenceService->create($request->validated(), $userId);
       // dispatch(new FetchUserFeedJob($userId));

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(UserPreference $userPreference)
    {
        $response = $this->userPreferenceService->show($userPreference->id);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserPreferenceRequest $request, UserPreference $userPreference)
    {
        $response = $this->userPreferenceService->update($request->validated(), $userPreference);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserPreference $userPreference)
    {
        $response = $this->userPreferenceService->delete($userPreference->id);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_NO_CONTENT);
    }

     /**
     * get preference by news source
     */
    public function getPreferenceBySource(string $newsSource)
    {
        $response = $this->userPreferenceService->getPreferenceBySource($newsSource);

        return $this->apiSuccessResponse($response, RESPONSE::HTTP_OK);
    }
}
