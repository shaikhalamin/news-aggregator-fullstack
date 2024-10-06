<?php

namespace App\Services\ResponseStore;

use App\Factories\Interfaces\NewsResponseStoreInterface;
use App\Services\Aggregator\GuardianApi as GuardianApiAggregator;
use App\Services\UserFeed\UserFeedService;

class GuardianApi implements NewsResponseStoreInterface
{

    public function __construct(private UserFeedService $userFeedService)
    {
    }

    public function store(int $userId, mixed $responseData, bool $isTopStories = false)
    {
        if (!empty($responseData) && !empty($responseData['response']['results'])) {

            foreach ($responseData['response']['results'] as $article) {
                $payload = GuardianApiAggregator::transform($article, $isTopStories, $userId);
                $this->userFeedService->create($payload);
            }
        }
    }
}
