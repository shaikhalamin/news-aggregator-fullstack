<?php

namespace App\Services\FeedGenerator;

use App\Jobs\StoreUserSourceNewsJob;
use App\Services\Aggregator\AggregatorType;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewsFeedGeneratorService
{
    public function __construct(private UserService $userService)
    {
    }
    public function generateNewsFeed(int $userId)
    {
        try {
            $user  = $this->userService->show($userId, ['preferences']);
            if (!$user) {
                return;
            }
            Log::info('[NewsFeedGeneratorService]: first name  ===> : ' . $user->first_name);
            $userPreferences = $user->preferences;
        } catch (Throwable $th) {
            Log::info('[NewsFeedGeneratorService]: [error]:   ===> : ' . $th->getMessage());
        }
    }
}
