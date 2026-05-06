<?php

namespace App\Repositories;

use App\DTOs\Resultado\ResultadoDto;
use App\Models\Resultado;
use Illuminate\Support\Collection;

class ResultadoRepository
{
    public function all(): Collection
    {
        $resultados = Resultado::with(['user', 'logro'])->get();
        return ResultadoDto::collection($resultados);
    }

    public function find(int $id): ?ResultadoDto
    {
        $resultado = Resultado::with(['user', 'logro'])->find($id);
        return $resultado ? ResultadoDto::fromModel($resultado) : null;
    }

    public function create(array $data): ResultadoDto
    {
        $resultado = Resultado::create($data);
        return ResultadoDto::fromModel($resultado);
    }

    public function update(int $id, array $data): bool
    {
        $resultado = Resultado::find($id);
        return $resultado ? $resultado->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $resultado = Resultado::find($id);
        return $resultado ? $resultado->delete() : false;
    }
}
