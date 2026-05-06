<?php

namespace App\Services;

use App\Repositories\ResultadoRepository;
use App\DTOs\Resultado\ResultadoDto;
use Illuminate\Support\Collection;

class ResultadoService
{
    public function __construct(
        private readonly ResultadoRepository $resultadoRepository
    ) {
    }

    public function getAllResultados(): Collection
    {
        return $this->resultadoRepository->all();
    }

    public function getResultadoById(int $id): ?ResultadoDto
    {
        return $this->resultadoRepository->find($id);
    }

    public function createResultado(array $data): ResultadoDto
    {
        return $this->resultadoRepository->create($data);
    }

    public function updateResultado(int $id, array $data): bool
    {
        return $this->resultadoRepository->update($id, $data);
    }

    public function deleteResultado(int $id): bool
    {
        return $this->resultadoRepository->delete($id);
    }
}
