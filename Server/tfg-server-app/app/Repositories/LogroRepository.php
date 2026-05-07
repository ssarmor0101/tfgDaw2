<?php

namespace App\Repositories;

use App\DTOs\Logro\LogroDto;
use App\Models\Logro;
use Illuminate\Support\Collection;

class LogroRepository
{
    public function all(): Collection
    {
        return Logro::all();
    }

    public function find(int $id): ?Logro
    {
        return Logro::find($id);
    }

    public function create(LogroDto $logroDto): Logro
    {
        return Logro::create($logroDto->toArrayWithoutNulls());
    }

    public function update(Logro $logro, LogroDto $logroDto): bool
    {
        return $logro->update($logroDto->toArrayWithoutNulls());
    }

    public function delete(Logro $logro): bool
    {
        return $logro->delete();
    }
}
