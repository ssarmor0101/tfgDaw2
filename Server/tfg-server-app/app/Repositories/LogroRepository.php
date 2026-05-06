<?php

namespace App\Repositories;

use App\DTOs\Logro\LogroDto;
use App\Models\Logro;
use Illuminate\Support\Collection;

class LogroRepository
{
    public function all(): Collection
    {
        $logros = Logro::with(['juego', 'resultados'])->get();
        return LogroDto::collection($logros);
    }

    public function find(int $id): ?LogroDto
    {
        $logro = Logro::with(['juego', 'resultados'])->find($id);
        return $logro ? LogroDto::fromModel($logro) : null;
    }

    public function create(array $data): LogroDto
    {
        $logro = Logro::create($data);
        return LogroDto::fromModel($logro);
    }

    public function update(int $id, array $data): bool
    {
        $logro = Logro::find($id);
        return $logro ? $logro->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $logro = Logro::find($id);
        return $logro ? $logro->delete() : false;
    }
}
