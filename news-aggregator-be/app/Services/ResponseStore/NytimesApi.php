<?php

namespace App\Services\ResponseStore;

use App\Factories\Interfaces\NewsResponseStoreInterface;
use App\Services\UserFeed\UserFeedService;
use App\Services\Aggregator\NytimesApi as NytimesApiAggregator;

class NytimesApi implements NewsResponseStoreInterface
{

    public function __construct(private UserFeedService $userFeedService)
    {
    }

    public function store(int $userId, mixed $responseData, bool $isTopStories = false)
    {
        if (!empty($responseData) && !empty($responseData['response']['docs'])) {

            foreach ($responseData['response']['docs'] as $article) {
                $payload = NytimesApiAggregator::transform($article, $isTopStories, $userId);
                $this->userFeedService->create($payload);
            }
        }
    }
}
