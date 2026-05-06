<?php

namespace App\Services;

use App\Repositories\JuegoRepository;
use App\DTOs\Juego\JuegoDto;
use Illuminate\Support\Collection;

class JuegoService
{
    public function __construct(
        private readonly JuegoRepository $juegoRepository
    ) {
    }

    public function getAllJuegos(): Collection
    {
        return $this->juegoRepository->all();
    }

    public function getJuegoById(int $id): ?JuegoDto
    {
        return $this->juegoRepository->find($id);
    }

    public function createJuego(array $data): JuegoDto
    {
        return $this->juegoRepository->create($data);
    }

    public function updateJuego(int $id, array $data): bool
    {
        return $this->juegoRepository->update($id, $data);
    }

    public function deleteJuego(int $id): bool
    {
        return $this->juegoRepository->delete($id);
    }
}