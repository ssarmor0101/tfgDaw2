<?php

namespace App\Repositories;

use App\DTOs\Juego\JuegoDto;
use App\Models\Juego;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    /**
     * Juegos ordenados por número de puntuaciones (más jugados = más populares).
     */
    public function getPopular(int $limit = 6): Collection
    {
        return Juego::withCount('puntuaciones')
            ->orderByDesc('puntuaciones_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Juegos ordenados por fecha de creación descendente (más recientes primero).
     */
    public function getRecent(int $limit = 6): Collection
    {
        return Juego::orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Búsqueda paginada con filtro por nombre y orden configurable.
     *
     * @param string $order  popular_desc | popular_asc | recent | oldest
     */
    public function search(string $query = '', string $order = 'popular_desc', int $page = 1, int $perPage = 12): LengthAwarePaginator
    {
        $builder = Juego::withCount('puntuaciones');

        if ($query !== '') {
            $builder->where('name', 'like', '%' . $query . '%');
        }

        switch ($order) {
            case 'popular_asc':
                $builder->orderBy('puntuaciones_count');
                break;
            case 'recent':
                $builder->orderByDesc('created_at');
                break;
            case 'oldest':
                $builder->orderBy('created_at');
                break;
            default: // popular_desc
                $builder->orderByDesc('puntuaciones_count');
                break;
        }

        return $builder->paginate($perPage, ['*'], 'page', $page);
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
