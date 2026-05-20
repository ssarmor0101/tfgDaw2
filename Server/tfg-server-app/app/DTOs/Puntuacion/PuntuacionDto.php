<?php

namespace App\DTOs\Puntuacion;

use App\DTOs\Dto;
use App\Models\Puntuacion;

class PuntuacionDto extends Dto
{
    public function __construct(
        public ?int $id = null,
        public ?int $user_id = null,
        public ?int $juego_id = null,
        public ?int $puntuacion = null,
        public ?array $user = null,
        public ?array $juego = null,
        public ?string $created_at = null,
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Puntuacion $item */
        return new self(
            id: $item->id,
            user_id: $item->user_id,
            juego_id: $item->juego_id,
            puntuacion: $item->puntuacion,
            user: $item->user?->toArray(),
            juego: $item->juego?->toArray(),
            created_at: $item->created_at?->toISOString(),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            user_id: $data['user_id'] ?? null,
            juego_id: $data['juego_id'] ?? null,
            puntuacion: $data['puntuacion'] ?? null,
            user: $data['user'] ?? null,
            juego: $data['juego'] ?? null,
            created_at: $data['created_at'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'juego_id' => $this->juego_id,
            'puntuacion' => $this->puntuacion,
            'user' => $this->user,
            'juego' => $this->juego,
            'created_at' => $this->created_at,
        ];
    }
}
