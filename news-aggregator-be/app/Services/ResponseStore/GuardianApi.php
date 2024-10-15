<?php

namespace App\Services\ResponseStore;

use App\Factories\Interfaces\NewsResponseStoreInterface;
use App\Services\Aggregator\GuardianApi as GuardianApiAggregator;
use App\Services\UserFeed\UserFeedService;

class GuardianApi implements NewsResponseStoreInterface
{

    public function __construct(private UserFeedService $userFeedService) {}

    public function store(array $responseData = [])
    {
        if (!empty($responseData)) {
            foreach ($responseData as $newsFeed) {
                $this->userFeedService->create($newsFeed);
            }
        }
    }
}
