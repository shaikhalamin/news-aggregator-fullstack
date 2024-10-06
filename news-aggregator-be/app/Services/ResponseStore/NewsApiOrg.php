<?php

namespace App\Services\ResponseStore;

use App\Factories\Interfaces\NewsResponseStoreInterface;
use App\Services\UserFeed\UserFeedService;
use App\Services\Aggregator\NewsApiOrg as NewsApiOrgAggregator;


class NewsApiOrg implements NewsResponseStoreInterface
{

    public function __construct(private UserFeedService $userFeedService)
    {
    }

    public function store(int $userId, mixed $responseData, bool $isTopStories = false)
    {
        if (!empty($responseData) && $responseData->totalResults > 0) {
            foreach ($responseData->articles as $article) {
                $payload = NewsApiOrgAggregator::transform($article, $isTopStories, $userId);
                $this->userFeedService->create($payload);
            }
        }
    }
}
