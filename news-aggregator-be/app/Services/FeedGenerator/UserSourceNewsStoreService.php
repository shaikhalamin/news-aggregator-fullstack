<?php

namespace App\Services\FeedGenerator;

use App\Factories\NewsApiFactory;
use App\Factories\ResponseStoreFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserSourceNewsStoreService
{
    public function storeNews($userId, $preference, $preferenceType)
    {
        Log::info('[UserSourceNewsStoreService]: processing source preference  ===> : ' . $preference);
        $sourceConfig = config('news_agrregator.sources' . '.' . $preference);

        // News source factory instance to fetch news from news source
        $newsSourceFactory = NewsApiFactory::create($preference);
        $newsSources = $sourceConfig['news_sources'];

        $headlineFetchAble = $sourceConfig['fetch_headline'];

        // Factory Instance to store news dynamically
        $newsResponseStoreFactory = ResponseStoreFactory::create($preference);

        foreach ($newsSources as $newsSource) {
            $params = ['sources' => $newsSource];
            Log::info('[UserSourceNewsStoreService]: internal source news calling  ===> : ' . $newsSource);

            if ($headlineFetchAble) {
                //fetching and saving headlines
                $newsFetchHeadLineResponseData = $newsSourceFactory->headlines($params);
                $newsResponseStoreFactory->store($userId, $newsFetchHeadLineResponseData, $headlineFetchAble);
            }
            // //fetching and saving all 
            $newsFetchAllResponseData = $newsSourceFactory->all($params);

            // Storing data to database
            $newsResponseStoreFactory->store($userId, $newsFetchAllResponseData, false);
        }
    }
}
