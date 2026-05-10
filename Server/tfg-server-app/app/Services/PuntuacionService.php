<?php

namespace App\Services;

use App\DTOs\Juego\JuegoDto;
use App\Repositories\PuntuacionRepository;
use App\DTOs\Puntuacion\PuntuacionDto;
use App\DTOs\User\UserDto;
use App\Models\Puntuacion;
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

    public function createPuntuacion(PuntuacionDto $puntuacionDto): PuntuacionDto
    {
        $puntuacion = $this->puntuacionRepository->create($puntuacionDto);
        return PuntuacionDto::fromModel($puntuacion);
    }

    public function updatePuntuacion(Puntuacion $puntuacion, PuntuacionDto $puntuacionDto): bool
    {
        return $this->puntuacionRepository->update($puntuacion, $puntuacionDto);
    }

    public function deletePuntuacion(Puntuacion $puntuacion): bool
    {
        return $this->puntuacionRepository->delete($puntuacion);
    }

    public function getPuntuacionesByUserIdJuegoId(UserDto $userDto, JuegoDto $juegoDto): Collection
    {
        return PuntuacionDto::collection($this->puntuacionRepository->getByUserIdJuegoId($userDto, $juegoDto));
    }

    public function getPuntuacionesByUserId(UserDto $userDto): Collection
    {
        return PuntuacionDto::collection($this->puntuacionRepository->getByUserId($userDto));
    }
}