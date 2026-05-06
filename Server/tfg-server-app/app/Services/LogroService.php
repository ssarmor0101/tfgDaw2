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
        return $this->logroRepository->all();
    }

    public function getLogroById(int $id): ?LogroDto
    {
        return $this->logroRepository->find($id);
    }

    public function createLogro(array $data): LogroDto
    {
        return $this->logroRepository->create($data);
    }

    public function updateLogro(int $id, array $data): bool
    {
        return $this->logroRepository->update($id, $data);
    }

    public function deleteLogro(int $id): bool
    {
        return $this->logroRepository->delete($id);
    }
}
