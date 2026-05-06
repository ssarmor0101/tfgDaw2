<?php

namespace App\Services;

use App\Repositories\PuntuacionRepository;
use App\DTOs\Puntuacion\PuntuacionDto;
use Illuminate\Support\Collection;

class PuntuacionService
{
    public function __construct(
        private readonly PuntuacionRepository $puntuacionRepository
    ) {
    }

    public function getAllPuntuaciones(): Collection
    {
        return $this->puntuacionRepository->all();
    }

    public function getPuntuacionById(int $id): ?PuntuacionDto
    {
        return $this->puntuacionRepository->find($id);
    }

    public function createPuntuacion(array $data): PuntuacionDto
    {
        return $this->puntuacionRepository->create($data);
    }

    public function updatePuntuacion(int $id, array $data): bool
    {
        return $this->puntuacionRepository->update($id, $data);
    }

    public function deletePuntuacion(int $id): bool
    {
        return $this->puntuacionRepository->delete($id);
    }
}
