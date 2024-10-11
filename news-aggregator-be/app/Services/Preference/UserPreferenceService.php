<?php

namespace App\Services\Preference;

use App\Models\UserPreference;

class UserPreferenceService
{
    public function list(int $userId)
    {
        return UserPreference::where(['user_id' => $userId])->orderBy('updated_at', 'desc')->get();
    }

    public function create(array $data, int $userId)
    {
        $payload = [
            ...$data,
            'user_id' => $userId
        ];

        return UserPreference::updateOrCreate(['source' => $data['source'], 'user_id' => $userId], $payload);
    }

    public function show(int $id)
    {
        return UserPreference::find($id);
    }

    public function update(array $data, $userPreference)
    {
        $metadata = $userPreference->metadata;
        $payload = $metadata;

        if (!empty($data['metadata']['categories'])) {
            $payload['categories'] = $data['metadata']['categories'];
        }
        if (!empty($data['metadata']['authors'])) {
            $payload['authors'] = $data['metadata']['authors'];
        }
        $payload['metadata'] = $payload;

        if (!empty($data['user_id'])) {
            $payload['user_id'] = $data['user_id'];
        }
        $userPreference->update($payload);

        return $userPreference->refresh();
    }

    public function delete(int $id)
    {
        return $this->show($id)->delete();
    }
}
