<?php

namespace App\Repositories;

use App\DTOs\Juego\JuegoDto;
use App\Models\Juego;
use Illuminate\Support\Collection;

class JuegoRepository
{
    public function all(): Collection
    {
        return Juego::all();
    }

    public function find(int $id): ?Juego
    {
        return Juego::find($id);
    }

    public function create(JuegoDto $juegoDto): Juego
    {
        return Juego::create($juegoDto->toArrayWithoutNulls());
    }

    public function update(Juego $juego, JuegoDto $juegoDto): bool
    {
        return $juego->update($juegoDto->toArrayWithoutNulls());
    }

    public function delete(Juego $juego): bool
    {
        return $juego->delete();
    }
}
