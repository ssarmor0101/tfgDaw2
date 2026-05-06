<?php

namespace App\Repositories;

use App\DTOs\Amigo\AmigoDto;
use App\Models\Amigo;
use Illuminate\Support\Collection;

class AmigoRepository
{
    public function all(): Collection
    {
        $amigos = Amigo::with(['user', 'friend'])->get();
        return AmigoDto::collection($amigos);
    }

    public function find(int $id): ?AmigoDto
    {
        $amigo = Amigo::with(['user', 'friend'])->find($id);
        return $amigo ? AmigoDto::fromModel($amigo) : null;
    }

    public function create(array $data): AmigoDto
    {
        $amigo = Amigo::create($data);
        return AmigoDto::fromModel($amigo);
    }

    public function update(int $id, array $data): bool
    {
        $amigo = Amigo::find($id);
        return $amigo ? $amigo->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $amigo = Amigo::find($id);
        return $amigo ? $amigo->delete() : false;
    }

    public function getForUser(int $userId): Collection
    {
        $amigos = Amigo::with(['user', 'friend'])
            ->where('user_id', $userId)
            ->orWhere('friend_id', $userId)
            ->get();
        return AmigoDto::collection($amigos);
    }
}
