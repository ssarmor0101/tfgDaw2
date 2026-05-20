<?php

namespace App\Services;

use App\Repositories\JuegoRepository;
use App\DTOs\Juego\JuegoDto;
use App\Models\Juego;
use Illuminate\Support\Collection;

class JuegoService
{
    public function __construct(
        private readonly JuegoRepository $juegoRepository
    ) {
    }

    public function getAllJuegos(): Collection
    {
        return JuegoDto::collection($this->juegoRepository->all());
    }

    public function getPopularJuegos(int $limit = 6): Collection
    {
        return JuegoDto::collection($this->juegoRepository->getPopular($limit));
    }

    public function getRecentJuegos(int $limit = 6): Collection
    {
        return JuegoDto::collection($this->juegoRepository->getRecent($limit));
    }

    public function searchJuegos(string $query = '', string $order = 'popular_desc', int $page = 1, int $perPage = 12): array
    {
        $paginator = $this->juegoRepository->search($query, $order, $page, $perPage);

        return [
            'items'        => JuegoDto::collection($paginator->items())->values(),
            'total'        => $paginator->total(),
            'per_page'     => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page'    => $paginator->lastPage(),
        ];
    }

    public function getJuegoById(int $id): ?JuegoDto
    {
        $juego = $this->juegoRepository->find($id);
        return $juego ? JuegoDto::fromModel($juego) : null;
    }

    public function createJuego(JuegoDto $juegoDto): JuegoDto
    {
        $juego = $this->juegoRepository->create($juegoDto);
        return JuegoDto::fromModel($juego);
    }

    public function updateJuego(Juego $juego, JuegoDto $juegoDto): bool
    {
        return $this->juegoRepository->update($juego, $juegoDto);
    }

    public function deleteJuego(Juego $juego): bool
    {
        return $this->juegoRepository->delete($juego);
    }
}