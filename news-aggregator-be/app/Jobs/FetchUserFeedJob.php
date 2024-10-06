<?php

namespace App\Jobs;

use App\Services\FeedGenerator\NewsFeedGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchUserFeedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(NewsFeedGeneratorService $newsFeedGeneratorService): void
    {
        Log::info('Processing [FetchUserFeedJob]: data  ===> : ' . $this->userId);
        $newsFeedGeneratorService->generateNewsFeed($this->userId);
    }
}
