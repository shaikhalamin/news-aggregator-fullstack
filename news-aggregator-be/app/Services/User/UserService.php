<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function list()
    {

        return User::orderBy('updated_at', 'desc')->paginate(50);
    }

    public function create(array $data)
    {
        $payload = [
            ...$data,
            'password' => Hash::make($data['password']),
            'is_active' => true
        ];
        return User::create($payload);
    }

    public function show(int $id, array $relations = ['preferences'])
    {
        $user = User::with($relations)->find($id);

        return $user;
    }

    public function update(array $data, $user)
    {
        $user->update($data);

        return $user->refresh();
    }

    public function delete(int $id): bool
    {
        return $this->show($id)->delete();
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function updateRefreshToken(int $id, string | null $token)
    {
        $user = $this->show($id);
        $user->update(['refresh_token' => $token]);
        return $user;
    }

    public function findByRefreshToken(string $token)
    {
        $user =  User::where('refresh_token', $token)->first();
        return $user;
    }

    public function searchUser(string $searchTerm)
    {
        return User::query()
            ->orWhere('email', 'LIKE', "%{$searchTerm}%")
            ->paginate(50);
    }
}
