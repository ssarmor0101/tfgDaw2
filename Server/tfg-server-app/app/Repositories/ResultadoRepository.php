<?php

namespace App\Repositories;

use App\DTOs\Resultado\ResultadoDto;
use App\DTOs\User\UserDto;
use App\Models\Resultado;
use Illuminate\Support\Collection;

class ResultadoRepository
{
    public function all(): Collection
    {
        return Resultado::all();
    }

    public function find(int $id): ?Resultado
    {
        return Resultado::find($id);
    }

    public function create(ResultadoDto $resultadoDto): Resultado
    {
        return Resultado::create($resultadoDto->toArrayWithoutNulls());
    }

    public function update(Resultado $resultado, ResultadoDto $resultadoDto): bool
    {
        return $resultado->update($resultadoDto->toArrayWithoutNulls());
    }

    public function delete(Resultado $resultado): bool
    {
        return $resultado->delete();
    }

    public function getByUserId(UserDto $userDto): Collection
    {
        return Resultado::where('user_id', $userDto->id)->get();
    }
}
