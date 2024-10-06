<?php

namespace App\Services\SearchFilter;

use App\Factories\NewsApiFactory;
use App\Services\Aggregator\AggregatorType;

class SearchFilterService
{
    public function getCategoriesBySource(string $source)
    {
        $sourceConfig = config('news_agrregator.sources' . '.' . $source);
        if (empty($sourceConfig) || empty($sourceConfig['categories'])) {
            return [];
        }

        return $sourceConfig['categories'];
    }

    public function filterSearch(array $params)
    {
        if (!empty($params['source'])) {
            $source = $params['source'];
            $sourceFactory = NewsApiFactory::create($source);
            $fetchAll = $sourceFactory->all($params);
            $response = $sourceFactory->transformArray($fetchAll);
            return $response;
        }

        $responseList = [];
        $sourceList = [
            AggregatorType::NEWS_API_ORG,
            AggregatorType::GURDIAN_API,
            AggregatorType::NYTIMES_API
        ];
        foreach ($sourceList as $source) {
            $sourceFactory = NewsApiFactory::create($source);
            $fetchAll = $sourceFactory->all($params);
            $response = $sourceFactory->transformArray($fetchAll);
            $responseList = array_merge($responseList, $response);
        }
        //dd($responseList);

        return $responseList;
    }
}
