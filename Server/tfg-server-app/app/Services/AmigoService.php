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
        return AmigoDto::collection($this->amigoRepository->all());
    }

    public function getAmigosByUserId(int $userId): Collection
    {
        return AmigoDto::collection($this->amigoRepository->getForUser($userId));
    }

    public function getAmigoById(int $id): ?AmigoDto
    {
        $amigo = $this->amigoRepository->find($id);
        return $amigo ? AmigoDto::fromModel($amigo) : null;
    }

    public function createAmigo(array $data): AmigoDto
    {
        $amigoDto = AmigoDto::fromArray($data);
        $amigo = $this->amigoRepository->create($amigoDto);
        return AmigoDto::fromModel($amigo);
    }

    public function updateAmigo(int $id, array $data): bool
    {
        $amigo = $this->amigoRepository->find($id);
        if (!$amigo) {
            return false;
        }
        $amigoDto = AmigoDto::fromArray($data);
        return $this->amigoRepository->update($amigo, $amigoDto);
    }

    public function deleteAmigo(int $id): bool
    {
        $amigo = $this->amigoRepository->find($id);
        if (!$amigo) {
            return false;
        }
        return $this->amigoRepository->delete($amigo);
    }
}
