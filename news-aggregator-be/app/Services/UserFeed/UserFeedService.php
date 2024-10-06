<?php

namespace App\Services\UserFeed;

use App\Models\UserFeed;

class UserFeedService
{
    public function list(int $userId)
    {
        return UserFeed::where('user_id', $userId)->orderBy('updated_at', 'desc')->paginate(50);
    }

    public function create(array $data)
    {
        $payload = [
            ...$data,

        ];
        return UserFeed::create($payload);
    }

    public function show(int $id, array $relations = [])
    {
        $userFeed = UserFeed::with($relations)->find($id);

        return $userFeed;
    }

    public function update(array $data, $userFeed)
    {
        $userFeed->update($data);

        return $userFeed->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->show($id)->delete();
    }
}
