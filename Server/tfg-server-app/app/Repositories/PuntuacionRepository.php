<?php

namespace App\Repositories;

use App\DTOs\Puntuacion\PuntuacionDto;
use App\Models\Puntuacion;
use Illuminate\Support\Collection;

class PuntuacionRepository
{
    public function all(): Collection
    {
        $puntuaciones = Puntuacion::with(['user', 'juego'])->get();
        return PuntuacionDto::collection($puntuaciones);
    }

    public function find(int $id): ?PuntuacionDto
    {
        $puntuacion = Puntuacion::with(['user', 'juego'])->find($id);
        return $puntuacion ? PuntuacionDto::fromModel($puntuacion) : null;
    }

    public function create(array $data): PuntuacionDto
    {
        $puntuacion = Puntuacion::create($data);
        return PuntuacionDto::fromModel($puntuacion);
    }

    public function update(int $id, array $data): bool
    {
        $puntuacion = Puntuacion::find($id);
        return $puntuacion ? $puntuacion->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $puntuacion = Puntuacion::find($id);
        return $puntuacion ? $puntuacion->delete() : false;
    }
}
