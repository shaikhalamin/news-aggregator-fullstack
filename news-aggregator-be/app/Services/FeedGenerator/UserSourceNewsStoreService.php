<?php

namespace App\Services\FeedGenerator;

use App\Factories\NewsApiFactory;
use App\Jobs\NewsFetchAndStoreJob;
use App\Services\Aggregator\AggregatorType;
use App\Services\Preference\UserPreferenceService;
use App\Services\PreferenceSaveLog\PreferenceSaveLogService;
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

    public function __construct(private UserPreferenceService $userPreferenceService, private UserFeedService $userFeedService, private PreferenceSaveLogService $preferenceSaveLogService) {}

    public function prepareNewsSourcePreferences(int $userId, string $newsSource)
    {
        Log::info('[UserSourceNewsStoreService]: processing source preference  ===> : ' . $newsSource);
        $userPreferenceByNewsSource = $this->userPreferenceService->getPreferenceBySource($newsSource, $userId);

        if (!is_null($userPreferenceByNewsSource)) {
            $newsSourceFactory = NewsApiFactory::create($newsSource);
            $userPreferenceParams = $newsSourceFactory->prepareParams($userPreferenceByNewsSource->toArray());

            $dispatchingQueues = !empty($this->queueNames[$newsSource])  ? $this->queueNames[$newsSource] : ['default'];

            if (count($userPreferenceParams) > 0) {
                foreach ($userPreferenceParams as $preferenceParam) {
                    $alreadyProcessed =  $this->checkAlreadyProcessed($userId, $newsSource, $preferenceParam);
                    if (!$alreadyProcessed) {
                        $randomQueue = $dispatchingQueues[mt_rand(0, 2)];

                        dispatch(new NewsFetchAndStoreJob($userId, $newsSource, $preferenceParam))
                            ->onQueue($randomQueue)
                            ->delay($newsSourceFactory->apiDelay());
                    }
                }
            }
        }
    }

    private function checkAlreadyProcessed(int $userId, string $newsSource, array $userPreferenceByNewsSource = [])
    {
        $queryPayload = [];

        Log::info('userPreferenceByNewsSource before job dispatch: ', ['preference' => $userPreferenceByNewsSource]);

        try {

            if (!empty($userPreferenceByNewsSource['category'])) {
                $queryPayload['category'] = $userPreferenceByNewsSource['category'];
            }

            if (!empty($userPreferenceByNewsSource['author'])) {
                $queryPayload['author'] = $userPreferenceByNewsSource['author'];
            }

            $paramsForSaveLogChecking = $this->preferenceSaveLogService->getLogByCategoryOrAuthor($newsSource, $userId, $queryPayload);

            Log::info('DB fetched result', ['fetchedResult' => !is_null($paramsForSaveLogChecking) ? $paramsForSaveLogChecking->toArray() : null]);

            if (!is_null($paramsForSaveLogChecking)) {
                Log::info('Already fetched : ', ['userId' => $userId, 'source' => $newsSource, $queryPayload]);
                return true;
            }

            $queryPayload['source'] = $newsSource;
            $queryPayload['is_fetched'] = 1;
            Log::info('Inserting preference save log : ', ['userId' => $userId, 'source' => $newsSource, $queryPayload]);
            $this->preferenceSaveLogService->create($queryPayload, $userId);

            return false;
        } catch (Throwable $th) {

            Log::info('Saving data error of preferenceSaveLogService ', ['queryPayload' => $queryPayload, 'error' => $th->getMessage()]);
            return true;
        }
    }

    public function fetchNewsAndStore(int $userId, string $newsSource, array $params = [])
    {
        //Log::info('Source and Category For Initial Call : ', ['category' => !empty($params['category']) ? $params['category'] : '', 'source' => $newsSource]);
        $newsSourceFactory = NewsApiFactory::create($newsSource);
        $newsFetchAllResponseData = $newsSourceFactory->all($params);
        $transformedResult = $newsSourceFactory->transformArray($newsFetchAllResponseData, $userId, $params);
        $this->store($transformedResult['result']);

        $initCall = $params['callInit'];

        if ($initCall === true && !empty($transformedResult['meta']) && !empty($transformedResult['meta']['pageToIterate'])) {
            Log::info(
                'Processing [UserSourceNewsStoreService->fetchNewsAndStore]: initial metadata remaining call  ===> : ',
                [
                    'source' => $newsSource,
                    'category' => !empty($params['category']) ? $params['category'] : '',
                    'meta' => $transformedResult['meta']
                ]
            );

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
