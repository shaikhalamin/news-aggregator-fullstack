<?php

namespace App\Services\FeedGenerator;

use App\Factories\NewsApiFactory;
use App\Factories\ResponseStoreFactory;
use App\Jobs\NewsFetchAndStoreJob;
use App\Services\Preference\UserPreferenceService;
use App\Services\UserFeed\UserFeedService;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserSourceNewsStoreService
{
    public function __construct(private UserPreferenceService $userPreferenceService, private UserFeedService $userFeedService) {}

    public function prepareNewsSourcePreferences(int $userId, string $newsSource)
    {
        Log::info('[UserSourceNewsStoreService]: processing source preference  ===> : ' . $newsSource);
        $userPreferenceByNewsSource = $this->userPreferenceService->getPreferenceBySource($newsSource, $userId);

        if (!is_null($userPreferenceByNewsSource)) {
            $newsSourceFactory = NewsApiFactory::create($newsSource);
            $userPreferenceParams = $newsSourceFactory->prepareParams($userPreferenceByNewsSource->toArray());
            foreach ($userPreferenceParams as $preferenceParam) {
                dispatch(new NewsFetchAndStoreJob($userId, $newsSource, $preferenceParam));
            }
        }
    }

    public function fetchNewsAndStore(int $userId, string $newsSource, array $params = [])
    {
        $newsSourceFactory = NewsApiFactory::create($newsSource);
        $newsFetchAllResponseData = $newsSourceFactory->all($params);
        $transformedResult = $newsSourceFactory->transformArray($newsFetchAllResponseData, $userId);

        if ($transformedResult['meta']['pageToIterate'] > 0) {
            $indexToStart = $transformedResult['meta']['page'] + 1;
            $numberOfPages = $transformedResult['meta']['pageToIterate'];
            for ($i = $indexToStart; $i < $numberOfPages; $i++) {
                $preferenceParam = [
                    ...$params,
                    'page' => $i
                ];
                dispatch(new NewsFetchAndStoreJob($userId, $newsSource, $preferenceParam));
            }
        }

        $this->store($transformedResult['result']);
    }


    public function store(array $responseData = [])
    {
        if (!empty($responseData)) {
            foreach ($responseData as $newsFeed) {
                $this->userFeedService->create($newsFeed);
            }
        }
    }
}
