<?php

namespace App\Repositories;

use App\DTOs\User\UserDto;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    public function all(): Collection
    {
        $users = User::with(['rol'])->get();
        return UserDto::collection($users);
    }

    public function find(int $id): ?UserDto
    {
        $user = User::with(['rol'])->find($id);
        return $user ? UserDto::fromModel($user) : null;
    }

    public function create(array $data): UserDto
    {
        $user = User::create($data);
        return UserDto::fromModel($user);
    }

    public function update(int $id, array $data): bool
    {
        $user = User::find($id);
        return $user ? $user->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $user = User::find($id);
        return $user ? $user->delete() : false;
    }
}
