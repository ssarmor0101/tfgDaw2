<?php

namespace App\Repositories;

use App\DTOs\Puntuacion\PuntuacionDto;
use App\DTOs\User\UserDto;
use App\Models\Puntuacion;
use Illuminate\Support\Collection;

class PuntuacionRepository
{
    public function all(): Collection
    {
        return Puntuacion::all();
    }

    public function find(int $id): ?Puntuacion
    {
        return Puntuacion::find($id);
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

    public function getByUserId(UserDto $userDto): Collection
    {
        return Puntuacion::where('user_id', $userDto->id)->get();
    }
}