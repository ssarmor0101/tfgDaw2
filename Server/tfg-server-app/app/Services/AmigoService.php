<?php

namespace App\Services;

use App\Repositories\AmigoRepository;
use App\DTOs\Amigo\AmigoDto;
use Illuminate\Support\Collection;

class AmigoService
{
    public function __construct(
        private readonly AmigoRepository $amigoRepository
    ) {
    }

    public function getAllAmigos(): Collection
    {
        return $this->amigoRepository->all();
    }

    public function getAmigosByUserId(int $userId): Collection
    {
        return $this->amigoRepository->getForUser($userId);
    }

    public function getAmigoById(int $id): ?AmigoDto
    {
        return $this->amigoRepository->find($id);
    }

    public function createAmigo(array $data): AmigoDto
    {
        return $this->amigoRepository->create($data);
    }

    public function updateAmigo(int $id, array $data): bool
    {
        return $this->amigoRepository->update($id, $data);
    }

    public function deleteAmigo(int $id): bool
    {
        return $this->amigoRepository->delete($id);
    }
}
