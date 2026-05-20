<?php

namespace App\Services;

use App\Repositories\LogroRepository;
use App\DTOs\Logro\LogroDto;
use App\Models\Logro;
use Illuminate\Support\Collection;

class LogroService
{
    public function __construct(
        private readonly LogroRepository $logroRepository
    ) {
    }

    public function getAllLogros(): Collection
    {
        return LogroDto::collection($this->logroRepository->all());
    }

    public function getLogrosByJuegoId(int $juegoId): Collection
    {
        return LogroDto::collection($this->logroRepository->allByJuego($juegoId));
    }

    public function getLogroById(int $id): ?LogroDto
    {
        $logro = $this->logroRepository->find($id);
        return $logro ? LogroDto::fromModel($logro) : null;
    }

    public function createLogro(LogroDto $logroDto): LogroDto
    {
        $logro = $this->logroRepository->create($logroDto);
        return LogroDto::fromModel($logro);
    }

    public function updateLogro(Logro $logro, LogroDto $logroDto): bool
    {
        return $this->logroRepository->update($logro, $logroDto);
    }

    public function deleteLogro(Logro $logro): bool
    {
        return $this->logroRepository->delete($logro);
    }
}
