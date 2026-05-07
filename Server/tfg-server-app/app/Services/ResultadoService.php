<?php

namespace App\Services;

use App\Repositories\ResultadoRepository;
use App\DTOs\Resultado\ResultadoDto;
use App\Models\Resultado;
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

    public function createResultado(ResultadoDto $resultadoDto): ResultadoDto
    {
        $resultado = $this->resultadoRepository->create($resultadoDto);
        return ResultadoDto::fromModel($resultado);
    }

    public function updateResultado(Resultado $resultado, ResultadoDto $resultadoDto): bool
    {
        return $this->resultadoRepository->update($resultado, $resultadoDto);
    }

    public function deleteResultado(Resultado $resultado): bool
    {
        return $this->resultadoRepository->delete($resultado);
    }
}
