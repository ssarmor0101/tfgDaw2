<?php

namespace App\Repositories;

use App\DTOs\User\UserDto;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    public function all(): Collection
    {
        return User::all();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function create(UserDto $userDto): User
    {
        return User::create($userDto->toArrayWithoutNulls());
    }

    public function update(User $user, UserDto $userDto): bool
    {
        return $user->update($userDto->toArrayWithoutNulls());
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function findByName(string $name): ?User
    {
        return User::where('name', $name)->first();
    }

    public function getByName(string $name): Collection
    {
        return User::where('name', 'like', "%{$name}%")->get();
    }
}
