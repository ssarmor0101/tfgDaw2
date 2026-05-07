<?php

namespace App\Services;

use App\Repositories\AmigoRepository;
use App\DTOs\Amigo\AmigoDto;
use App\Models\Amigo;
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

    public function createAmigo(AmigoDto $amigoDto): AmigoDto
    {
        $amigo = $this->amigoRepository->create($amigoDto);
        return AmigoDto::fromModel($amigo);
    }

    public function updateAmigo(Amigo $amigo, AmigoDto $amigoDto): bool
    {
        return $this->amigoRepository->update($amigo, $amigoDto);
    }

    public function deleteAmigo(Amigo $amigo): bool
    {
        return $this->amigoRepository->delete($amigo);
    }
}
