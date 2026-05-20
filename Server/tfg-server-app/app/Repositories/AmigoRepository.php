<?php

namespace App\Repositories;

use App\DTOs\Amigo\AmigoDto;
use App\DTOs\User\UserDto;
use App\Helpers\AmigoHelper;
use App\Models\Amigo;
use Illuminate\Support\Collection;

class AmigoRepository
{
    public function all(): Collection
    {
        return Amigo::with('user', 'friend')->get();
    }

    public function find(int $id): ?Amigo
    {
        return Amigo::with('user', 'friend')->find($id);
    }

    /** @deprecated Use getConfirmedForUser or getPendingReceivedByUserId */
    public function getByUserId(UserDto $userDto): Collection
    {
        return Amigo::with('user', 'friend')
            ->where('user_id', $userDto->id)
            ->orWhere('friend_id', $userDto->id)
            ->get();
    }

    /** Confirmed friendships (receiver_id IS NULL) for the given user. */
    public function getConfirmedForUser(int $userId): Collection
    {
        return Amigo::with('user', 'friend')
            ->whereNull('receiver_id')
            ->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)->orWhere('friend_id', $userId);
            })
            ->get();
    }

    /** Pending requests received by the given user (receiver_id = userId). */
    public function getPendingReceivedByUserId(int $userId): Collection
    {
        return Amigo::with('user', 'friend')
            ->where('receiver_id', $userId)
            ->get();
    }

    /** Find an existing record for a (potentially unordered) pair. */
    public function findByPair(UserDto $userDto, UserDto $friendDto): ?Amigo
    {
        [$a, $b] = AmigoHelper::orderUsersPairForAmigo($userDto, $friendDto);
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

    /** Confirm a pending request by setting receiver_id to NULL. */
    public function accept(Amigo $amigo): bool
    {
        return $amigo->update(['receiver_id' => null]);
    }

    public function delete(Amigo $amigo): bool
    {
        return $amigo->delete();
    }

    public function getForUser(int $userId): Collection
    {
        return $this->getConfirmedForUser($userId);
    }
}
