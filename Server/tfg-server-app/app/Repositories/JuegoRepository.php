<?php

namespace App\Repositories;

use App\DTOs\Juego\JuegoDto;
use App\Models\Juego;
use Illuminate\Support\Collection;

class JuegoRepository
{
    public function all(): Collection
    {
        $juegos = Juego::with(['logros', 'puntuaciones'])->get();
        return JuegoDto::collection($juegos);
    }

    public function find(int $id): ?JuegoDto
    {
        $juego = Juego::with(['logros', 'puntuaciones'])->find($id);
        return $juego ? JuegoDto::fromModel($juego) : null;
    }

    public function create(array $data): JuegoDto
    {
        $juego = Juego::create($data);
        return JuegoDto::fromModel($juego);
    }

    public function update(int $id, array $data): bool
    {
        $juego = Juego::find($id);
        return $juego ? $juego->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $juego = Juego::find($id);
        return $juego ? $juego->delete() : false;
    }
}
