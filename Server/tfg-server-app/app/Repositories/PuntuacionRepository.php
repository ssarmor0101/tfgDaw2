<?php

namespace App\Repositories;

use App\DTOs\Juego\JuegoDto;
use App\DTOs\Puntuacion\PuntuacionDto;
use App\DTOs\User\UserDto;
use App\Models\Puntuacion;
use Illuminate\Support\Collection;

class PuntuacionRepository
{
    public function all(): Collection
    {
        return Puntuacion::with('user')->get();
    }

    public function find(int $id): ?Puntuacion
    {
        return Puntuacion::with('user')->find($id);
    }

    public function create(PuntuacionDto $puntuacionDto): Puntuacion
    {
        return Puntuacion::create($puntuacionDto->toArrayWithoutNulls());
    }

    public function update(Puntuacion $puntuacion, PuntuacionDto $puntuacionDto): bool
    {
        return $puntuacion->update($puntuacionDto->toArrayWithoutNulls());
    }

    public function delete(Puntuacion $puntuacion): bool
    {
        return $puntuacion->delete();
    }

    public function getByUserIdJuegoId(UserDto $userDto, JuegoDto $juegoDto): Collection
    {
        return Puntuacion::with('user')->where('user_id', $userDto->id)->where('juego_id', $juegoDto->id)->get();
    }

    public function getByUserId(UserDto $userDto): Collection
    {
        return Puntuacion::with('user')->where('user_id', $userDto->id)->get();
    }

    public function getByJuegoId(JuegoDto $juegoDto): Collection
    {
        return Puntuacion::with('user')->where('juego_id', $juegoDto->id)->get();
    }
}