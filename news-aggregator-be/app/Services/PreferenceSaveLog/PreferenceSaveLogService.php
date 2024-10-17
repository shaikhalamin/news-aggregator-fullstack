<?php

namespace App\Services\PreferenceSaveLog;

use App\Models\PreferenceSaveLog;
use Illuminate\Support\Facades\Log;

class PreferenceSaveLogService
{

    public function create(array $data = [], int $userId)
    {
        $payload = [
            ...$data,
            'user_id' => $userId
        ];

        return PreferenceSaveLog::create($payload);
    }

    public function getLogByCategoryOrAuthor(string $newsSource, int $userId, array $metaPreference = []): PreferenceSaveLog | null
    {
        $queryPayload = [
            'source' => $newsSource,
            'user_id' => $userId,
            'is_fetched' => 1,
        ];

        if (!empty($metaPreference['category'])) {
            $queryPayload = [
                ...$queryPayload,
                'category' => $metaPreference['category'],
            ];
        }

        if (!empty($metaPreference['author'])) {
            $queryPayload = [
                ...$queryPayload,
                'author' => $metaPreference['author'],
            ];
        }

        Log::info('Before query getLogByCategoryOrAuthor ', $queryPayload);

        return PreferenceSaveLog::where($queryPayload)->first();
    }
}
