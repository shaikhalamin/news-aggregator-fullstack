<?php

namespace App\Jobs;

use App\Services\FeedGenerator\UserSourceNewsStoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StoreUserSourceNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $userId, private string $newsSource) {}

    /**
     * Execute the job.
     */
    public function handle(UserSourceNewsStoreService $userSourceNewsStoreService): void
    {
        Log::info('Processing [StoreUserSourceNewsJob]: data  ===> : ', ['source' => $this->newsSource, 'userId' => $this->userId]);
        $userSourceNewsStoreService->prepareNewsSourcePreferences($this->userId, $this->newsSource);
    }
}
