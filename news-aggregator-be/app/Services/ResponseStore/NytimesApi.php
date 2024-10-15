<?php

namespace App\Services\ResponseStore;

use App\Factories\Interfaces\NewsResponseStoreInterface;
use App\Services\UserFeed\UserFeedService;
use App\Services\Aggregator\NytimesApi as NytimesApiAggregator;

class NytimesApi implements NewsResponseStoreInterface
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
