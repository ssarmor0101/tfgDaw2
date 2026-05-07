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
        return PuntuacionDto::collection($this->puntuacionRepository->all());
    }

    public function getPuntuacionById(int $id): ?PuntuacionDto
    {
        $puntuacion = $this->puntuacionRepository->find($id);
        return $puntuacion ? PuntuacionDto::fromModel($puntuacion) : null;
    }

    public function createPuntuacion(array $data): PuntuacionDto
    {
        $puntuacionDto = PuntuacionDto::fromArray($data);
        $puntuacion = $this->puntuacionRepository->create($puntuacionDto);
        return PuntuacionDto::fromModel($puntuacion);
    }

    public function updatePuntuacion(int $id, array $data): bool
    {
        $puntuacion = $this->puntuacionRepository->find($id);
        if (!$puntuacion) {
            return false;
        }
        $puntuacionDto = PuntuacionDto::fromArray($data);
        return $this->puntuacionRepository->update($puntuacion, $puntuacionDto);
    }

    public function deletePuntuacion(int $id): bool
    {
        $puntuacion = $this->puntuacionRepository->find($id);
        if (!$puntuacion) {
            return false;
        }
        return $this->puntuacionRepository->delete($puntuacion);
    }
}
