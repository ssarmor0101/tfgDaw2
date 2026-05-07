<?php

namespace App\DTOs\Puntuacion;

use App\DTOs\Dto;
use App\Models\Puntuacion;

class PuntuacionDto extends Dto
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?int $juego_id,
        public ?int $puntuacion
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Puntuacion $item */
        return new self(
            id: $item->id,
            user_id: $item->user_id,
            juego_id: $item->juego_id,
            puntuacion: $item->puntuacion
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            user_id: $data['user_id'] ?? null,
            juego_id: $data['juego_id'] ?? null,
            puntuacion: $data['puntuacion'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'juego_id' => $this->juego_id,
            'puntuacion' => $this->puntuacion
        ];
    }
}
