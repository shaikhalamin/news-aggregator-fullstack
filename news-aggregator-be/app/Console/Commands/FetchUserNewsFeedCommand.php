<?php

namespace App\Console\Commands;

use App\Services\Command\UserFeedRefreshService;
use Illuminate\Console\Command;

class FetchUserNewsFeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsfeed:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh user custom news feed periodically';

    /**
     * Execute the console command.
     */
    public function handle(UserFeedRefreshService $userFeedRefreshService)
    {
        // echo "News feed fetch command started";
        $userFeedRefreshService->refresh();
    }
}
