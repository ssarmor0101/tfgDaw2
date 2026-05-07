<?php

namespace App\Services;

use App\Repositories\LogroRepository;
use App\DTOs\Logro\LogroDto;
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

    public function getLogroById(int $id): ?LogroDto
    {
        $logro = $this->logroRepository->find($id);
        return $logro ? LogroDto::fromModel($logro) : null;
    }

    public function createLogro(array $data): LogroDto
    {
        $logroDto = LogroDto::fromArray($data);
        $logro = $this->logroRepository->create($logroDto);
        return LogroDto::fromModel($logro);
    }

    public function updateLogro(int $id, array $data): bool
    {
        $logro = $this->logroRepository->find($id);
        if (!$logro) {
            return false;
        }
        $logroDto = LogroDto::fromArray($data);
        return $this->logroRepository->update($logro, $logroDto);
    }

    public function deleteLogro(int $id): bool
    {
        $logro = $this->logroRepository->find($id);
        if (!$logro) {
            return false;
        }
        return $this->logroRepository->delete($logro);
    }
}
