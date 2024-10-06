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
            if (!count($userPreferences)) {
                Log::info('[NewsFeedGeneratorService]: processing deafult source list ===> : ');
                $preferenceList = [
                    AggregatorType::NEWS_API_ORG,
                    AggregatorType::GURDIAN_API,
                    AggregatorType::NYTIMES_API
                ];
                Log::info('[NewsFeedGeneratorService]: processing deafult source list ===> : ');
                foreach ($preferenceList as $preference) {
                    Log::info('[NewsFeedGeneratorService]: dispatching default source preference to store ===> : ' . $preference);
                    dispatch(new StoreUserSourceNewsJob($user->id, $preference, FeedPreferenceType::DEFAULT));
                }
            } else {
                foreach ($userPreferences as $preference) {
                    Log::info('[NewsFeedGeneratorService]: dispatching source preference to store ===> : ' . $preference->source);
                    dispatch(new StoreUserSourceNewsJob($user->id, $preference->source, FeedPreferenceType::PREFERED));
                }
            }
        } catch (Throwable $th) {
            Log::info('[NewsFeedGeneratorService]: [error]:   ===> : ' . $th->getMessage());
        }
    }
}
