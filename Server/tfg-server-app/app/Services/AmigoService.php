<?php

namespace App\Services;

use App\DTOs\Amigo\AmigoDto;
use App\DTOs\User\UserDto;
use App\Models\Amigo;
use App\Repositories\AmigoRepository;
use Illuminate\Support\Collection;

class AmigoService
{
    public function __construct(
        private readonly AmigoRepository $amigoRepository
    ) {
    }

    public function getAllAmigos(): Collection
    {
        return AmigoDto::collection($this->amigoRepository->all());
    }

    public function getAmigosByUserId(int $userId): Collection
    {
        return AmigoDto::collection($this->amigoRepository->getForUser($userId));
    }

    /** Confirmed friendships (receiver_id IS NULL) for the given user. */
    public function getConfirmedFriendsByUserId(int $userId): Collection
    {
        return AmigoDto::collection($this->amigoRepository->getConfirmedForUser($userId));
    }

    /** Pending requests received by the given user. */
    public function getPendingReceivedByUserId(int $userId): Collection
    {
        return AmigoDto::collection($this->amigoRepository->getPendingReceivedByUserId($userId));
    }

    /** Check if a relationship (any status) exists between two users. */
    public function findByUsers(UserDto $a, UserDto $b): ?AmigoDto
    {
        $amigo = $this->amigoRepository->findByPair($a, $b);
        return $amigo ? AmigoDto::fromModel($amigo) : null;
    }

    public function getAmigoById(int $id): ?AmigoDto
    {
        $amigo = $this->amigoRepository->find($id);
        return $amigo ? AmigoDto::fromModel($amigo) : null;
    }

    public function createAmigo(AmigoDto $amigoDto): AmigoDto
    {
        $amigo = $this->amigoRepository->create($amigoDto);
        return AmigoDto::fromModel($amigo->load('user', 'friend'));
    }

    public function updateAmigo(Amigo $amigo, AmigoDto $amigoDto): bool
    {
        return $this->amigoRepository->update($amigo, $amigoDto);
    }

    /** Accept a pending request: sets receiver_id to NULL. Returns refreshed DTO. */
    public function acceptAmigo(Amigo $amigo): AmigoDto
    {
        $this->amigoRepository->accept($amigo);
        return AmigoDto::fromModel($amigo->fresh()->load('user', 'friend'));
    }

    public function deleteAmigo(Amigo $amigo): bool
    {
        return $this->amigoRepository->delete($amigo);
    }
}
