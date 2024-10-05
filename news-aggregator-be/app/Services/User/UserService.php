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
        ];
        return User::create($payload);
    }

    public function show(string $id, array $relations = [])
    {
        $user = User::with($relations)->find($id);

        return $user;
    }

    public function update(array $data, $user)
    {
        $user->update($data);

        return $user->refresh();
    }

    public function delete(string $id): bool
    {
        return $this->show($id)->delete();
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function findByUserName(string $username, array $relations = [])
    {
        return User::with($relations)->where('username', $username)->first();
    }

    public function updateRefreshToken($id, $token)
    {
        $user = $this->show($id);
        $user->update(['refresh_token' => $token]);
        return $user;
    }

    public function findByRefreshToken($token)
    {
        $user =  User::where('refresh_token', $token)->first();
        return $user;
    }

    public function searchUser($searchTerm)
    {
        return User::query()
            ->where('username', 'LIKE', "%{$searchTerm}%")
            ->orWhere('email', 'LIKE', "%{$searchTerm}%")
            ->paginate(50);
    }
}
