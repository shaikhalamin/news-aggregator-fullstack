<?php

namespace App\Jobs;

use App\Services\FeedGenerator\UserSourceNewsStoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewsFetchAndStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $userId, private string $newsSource, private array $params = []) {}

    /**
     * Execute the job.
     */
    public function handle(UserSourceNewsStoreService $userSourceNewsStoreService): void
    {
        Log::info('Processing [NewsFetchAndStoreJob]: data  ===> : ', ['source' => $this->newsSource, 'params' => $this->params]);
        $userSourceNewsStoreService->fetchNewsAndStore($this->userId, $this->newsSource, $this->params);
    }

    public function failed(\Exception $exception)
    {
        // Log the failure reason
        Log::error('Job failed of NewsFetchAndStoreJob', [
            'job' => self::class,
            'exception_message' => $exception->getMessage(),
            'exception_trace' => $exception->getTraceAsString(),
        ]);
    }
}
