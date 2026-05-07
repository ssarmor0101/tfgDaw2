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
        return ResultadoDto::collection($this->resultadoRepository->all());
    }

    public function getResultadoById(int $id): ?ResultadoDto
    {
        $resultado = $this->resultadoRepository->find($id);
        return $resultado ? ResultadoDto::fromModel($resultado) : null;
    }

    public function createResultado(array $data): ResultadoDto
    {
        $resultadoDto = ResultadoDto::fromArray($data);
        $resultado = $this->resultadoRepository->create($resultadoDto);
        return ResultadoDto::fromModel($resultado);
    }

    public function updateResultado(int $id, array $data): bool
    {
        $resultado = $this->resultadoRepository->find($id);
        if (!$resultado) {
            return false;
        }
        $resultadoDto = ResultadoDto::fromArray($data);
        return $this->resultadoRepository->update($resultado, $resultadoDto);
    }

    public function deleteResultado(int $id): bool
    {
        $resultado = $this->resultadoRepository->find($id);
        if (!$resultado) {
            return false;
        }
        return $this->resultadoRepository->delete($resultado);
    }
}
