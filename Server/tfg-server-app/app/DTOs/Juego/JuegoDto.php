<?php

namespace App\DTOs\Juego;

use App\DTOs\Dto;
use App\Models\Juego;

class JuegoDto extends Dto
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $description = null
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Juego $item */
        return new self(
            id: $item->id,
            name: $item->name,
            description: $item->description
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
