<?php

namespace App\DTOs\Juego;

use App\DTOs\Dto;
use App\Models\Juego;

class JuegoDto extends Dto
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?int $puntuaciones_count = null,
        public ?string $created_at = null,
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Juego $item */
        return new self(
            id: $item->id,
            name: $item->name,
            description: $item->description,
            puntuaciones_count: $item->puntuaciones_count ?? null,
            created_at: $item->created_at?->toISOString(),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'puntuaciones_count' => $this->puntuaciones_count,
            'created_at' => $this->created_at,
        ];
    }
}
