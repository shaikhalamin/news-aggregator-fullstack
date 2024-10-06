<?php

namespace App\Services\Command;

use App\Jobs\FetchUserFeedJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserFeedRefreshService
{
    public function refresh()
    {
        try {
            foreach (User::where('is_active', true)->cursor() as $user) {
                if (!empty($user)) {
                    echo "User data found jod dispatched : " . $user->first_name . "\n";
                    Log::info('Processing [UserFeedRefreshService]: data  ===> : ' . $user->id);
                    dispatch(new FetchUserFeedJob($user->id));
                } else {
                    Log::info('Processing [UserFeedRefreshService]: empty user  ===> : ' . $user->id);
                }
            }
        } catch (Throwable $th) {
            Log::info('Processing [UserFeedRefreshService]: error  ===> : ' . $th->getMessage());
        }
    }
}
