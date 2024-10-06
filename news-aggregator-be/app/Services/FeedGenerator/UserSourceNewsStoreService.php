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
        $sourceFactory = NewsApiFactory::create($preference);
        $newsSources = $sourceConfig['news_sources'];

        $headlineFetchAble = $sourceConfig['fetch_headline'];

        $newsStoreFactory = ResponseStoreFactory::create($preference);

        foreach ($newsSources as $newsSource) {
            $params = ['sources' => $newsSource];
            Log::info('[UserSourceNewsStoreService]: internal source news calling  ===> : ' . $newsSource);

            if ($headlineFetchAble) {
                //fetching and saving headlines
                $fetchHeadLine = $sourceFactory->headlines($params);
                $newsStoreFactory->store($userId, $fetchHeadLine, $headlineFetchAble);
            }
            // //fetching and saving all 
            $fetchAll = $sourceFactory->all($params);

            //dd($fetchAll);
            $newsStoreFactory->store($userId, $fetchAll, false);
        }
    }
}
