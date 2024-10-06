<?php

namespace App\Jobs;

use App\Services\FeedGenerator\UserSourceNewsStoreService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StoreUserSourceNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $userId, private string $preference, private string $preferenceType)
    {
        // $this->userId = $userId;
        // $this->preference = $preference;
        // $this->preferenceType = $preferenceType;
    }

    /**
     * Execute the job.
     */
    public function handle(UserSourceNewsStoreService $userSourceNewsStoreService): void
    {
        $userSourceNewsStoreService->storeNews($this->userId, $this->preference, $this->preferenceType);
    }
}
