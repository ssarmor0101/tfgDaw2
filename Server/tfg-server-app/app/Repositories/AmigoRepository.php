<?php

namespace App\Repositories;

use App\DTOs\Amigo\AmigoDto;
use App\DTOs\User\UserDto;
use App\Models\Amigo;
use Illuminate\Support\Collection;

class AmigoRepository
{
    public function all(): Collection
    {
        return Amigo::all();
    }

    public function find(int $id): ?Amigo
    {
        return Amigo::find($id);
    }

    public function getByUserId(UserDto $userDto): Collection
    {
        return Amigo::where('user_id', $userDto->id)->orWhere('friend_id', $userDto->id)->get();
    }

    public function findByPair(UserDto $userDto, UserDto $friendDto): ?Amigo
    {
        [$a, $b] = Amigo::orderPair($userDto, $friendDto);
        return Amigo::where('user_id', $a->id)->where('friend_id', $b->id)->first();
    }

    public function create(AmigoDto $amigoDto): Amigo
    {
        return Amigo::create($amigoDto->toArrayWithoutNulls());
    }

    public function update(Amigo $amigo, AmigoDto $amigoDto): bool
    {
        return $amigo->update($amigoDto->toArrayWithoutNulls());
    }

    public function delete(Amigo $amigo): bool
    {
        return $amigo->delete();
    }

    public function getForUser(int $userId): Collection
    {
        return Amigo::where('user_id', $userId)
            ->orWhere('friend_id', $userId)
            ->get();
    }
}
