<?php

namespace App\Services\FeedGenerator;

use App\Factories\NewsApiFactory;
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
                dispatch(new NewsFetchAndStoreJob($userId, $newsSource, $preferenceParam))->delay(now()->addSeconds(14));
            }
        }
    }

    public function fetchNewsAndStore(int $userId, string $newsSource, array $params = [])
    {
        $newsSourceFactory = NewsApiFactory::create($newsSource);
        $newsFetchAllResponseData = $newsSourceFactory->all($params);
        $transformedResult = $newsSourceFactory->transformArray($newsFetchAllResponseData, $userId);
        $this->store($transformedResult['result']);

        // we need to set  break point to stop recalling 
        if (!empty($transformedResult['meta']) && !empty($transformedResult['meta']['pageToIterate']) && $transformedResult['meta']['pageToIterate'] > 0) {

            Log::info('Processing [UserSourceNewsStoreService->fetchNewsAndStore]: metadata  ===> : ', ['source' => $newsSource, 'meta' => $transformedResult['meta']]);

            $page = $transformedResult['meta']['page'];
            $total = $transformedResult['meta']['total'];
            $perPage = $transformedResult['meta']['perPage'];

            $remainingPage = intval(floor($total / $perPage));
            Log::info('Checking page and remaining val  ===> : ', [$page, $remainingPage]);
            if ($page === ($remainingPage - 1)) {
                return;
            }

            $indexToStart = $transformedResult['meta']['page'] + 1;
            $numberOfPages = $transformedResult['meta']['pageToIterate'];
            for ($i = $indexToStart; $i < $numberOfPages; $i++) {
                $preferenceParam = [
                    ...$params,
                    'page' => $i
                ];
                dispatch(new NewsFetchAndStoreJob($userId, $newsSource, $preferenceParam))->delay(now()->addSeconds(14));
            }
        }
    }


    public function store(array $responseData = [])
    {
        if (!empty($responseData)) {
            foreach ($responseData as $newsFeed) {
                try {
                    $this->userFeedService->create($newsFeed);
                } catch (Throwable $th) {
                    Log::info('Saving data error of type ' . $newsFeed['source'], ['error' => $th->getMessage()]);
                }
            }
        }
    }
}
