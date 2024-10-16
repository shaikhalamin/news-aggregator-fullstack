<?php

namespace App\Services\FeedGenerator;

use App\Factories\NewsApiFactory;
use App\Jobs\NewsFetchAndStoreJob;
use App\Services\Aggregator\AggregatorType;
use App\Services\Preference\UserPreferenceService;
use App\Services\UserFeed\UserFeedService;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserSourceNewsStoreService
{
    private $queueNames = [
        AggregatorType::GURDIAN_API => ['guardian_1', 'guardian_2', 'guardian_3'],
        AggregatorType::NYTIMES_API => ['nytimes_1', 'nytimes_2', 'nytimes_3'],
        AggregatorType::NEWS_API_ORG => ['newsapi_1', 'newsapi_2', 'newsapi_3']
    ];

    public function __construct(private UserPreferenceService $userPreferenceService, private UserFeedService $userFeedService) {}

    public function prepareNewsSourcePreferences(int $userId, string $newsSource)
    {
        Log::info('[UserSourceNewsStoreService]: processing source preference  ===> : ' . $newsSource);
        $userPreferenceByNewsSource = $this->userPreferenceService->getPreferenceBySource($newsSource, $userId);

        // check user feed table with user id and source and category to verify already processed or not  

        if (!is_null($userPreferenceByNewsSource)) {
            $newsSourceFactory = NewsApiFactory::create($newsSource);
            $userPreferenceParams = $newsSourceFactory->prepareParams($userPreferenceByNewsSource->toArray());
            $dispatchingQueues = !empty($this->queueNames[$newsSource])  ? $this->queueNames[$newsSource] : ['default'];

            if (count($userPreferenceParams) > 0) {
                foreach ($userPreferenceParams as $preferenceParam) {
                    $randomQueue = $dispatchingQueues[mt_rand(0, 2)];

                    dispatch(new NewsFetchAndStoreJob($userId, $newsSource, $preferenceParam))
                        ->onQueue($randomQueue)
                        ->delay($newsSourceFactory->apiDelay());
                }
            }
        }
    }

    public function fetchNewsAndStore(int $userId, string $newsSource, array $params = [])
    {
        $newsSourceFactory = NewsApiFactory::create($newsSource);
        $newsFetchAllResponseData = $newsSourceFactory->all($params);
        $transformedResult = $newsSourceFactory->transformArray($newsFetchAllResponseData, $userId);
        $this->store($transformedResult['result']);

        $initCall = $params['callInit'];

        if ($initCall === true && !empty($transformedResult['meta']) && !empty($transformedResult['meta']['pageToIterate'])) {
            Log::info('Processing [UserSourceNewsStoreService->fetchNewsAndStore]: metadata  ===> : ', ['source' => $newsSource, 'meta' => $transformedResult['meta']]);
            $currentPage = $transformedResult['meta']['page'];
            $numberOfPages = $transformedResult['meta']['pageToIterate'];
            $lengthToIterate = $numberOfPages + 1;

            if ($numberOfPages > 0 && $numberOfPages > $currentPage) {
                $dispatchingQueues = !empty($this->queueNames[$newsSource])  ? $this->queueNames[$newsSource] : ['default'];
                $indexToStart = $currentPage + 1;
                for ($i = $indexToStart; $i <= $lengthToIterate; $i++) {
                    $preferenceParam = [
                        ...$params,
                        'page' => $i,
                        'callInit' => false,
                    ];
                    $randomQueue = $dispatchingQueues[mt_rand(0, 2)];
                    dispatch(new NewsFetchAndStoreJob($userId, $newsSource, $preferenceParam))
                        ->onQueue($randomQueue)
                        ->delay($newsSourceFactory->apiDelay());
                }
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
