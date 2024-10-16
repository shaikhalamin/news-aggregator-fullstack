<?php

namespace App\Services\UserFeed;

use App\Models\UserFeed;
use Illuminate\Support\Carbon;

class UserFeedService
{
    public function list(int $userId)
    {
        return UserFeed::where('user_id', $userId)->orderBy('updated_at', 'desc')->paginate(50);
    }

    public function feedFilterList(array $params = [], int $userId)
    {
        $query = UserFeed::query();
        $query->where('user_id', $userId);

        if (isset($params['q']) && $params['q']) {
            $keyword = $params['q'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', "%{$keyword}%")
                    ->orWhere('content', 'LIKE', "%{$keyword}%");
            });
        }

        if (isset($params['startDate']) && isset($params['endDate'])) {

            $startDate =  Carbon::parse($params['startDate'])->format('Y-m-d');
            $endDate =  Carbon::parse($params['endDate'])->format('Y-m-d');
            $query->whereBetween('published_at', [$startDate, $endDate]);
        }

        if (isset($params['category']) && $params['category']) {
            $query->where('category', $params['category']);
        }

        if (isset($params['source']) && $params['source']) {
            $query->where('source', $params['source']);
        }

        $perPage = isset($params['per_page']) ? intval($params['per_page']) : 15;

        return $query->paginate($perPage > 100 ? 100 : $perPage);
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
