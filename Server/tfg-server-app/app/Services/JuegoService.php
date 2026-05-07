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