<?php

namespace App\Services;

use App\Repositories\JuegoRepository;
use App\DTOs\Juego\JuegoDto;
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

    public function createJuego(array $data): JuegoDto
    {
        $juegoDto = JuegoDto::fromArray($data);
        $juego = $this->juegoRepository->create($juegoDto);
        return JuegoDto::fromModel($juego);
    }

    public function updateJuego(int $id, array $data): bool
    {
        $juego = $this->juegoRepository->find($id);
        if (!$juego) {
            return false;
        }
        $juegoDto = JuegoDto::fromArray($data);
        return $this->juegoRepository->update($juego, $juegoDto);
    }

    public function deleteJuego(int $id): bool
    {
        $juego = $this->juegoRepository->find($id);
        if (!$juego) {
            return false;
        }
        return $this->juegoRepository->delete($juego);
    }
}