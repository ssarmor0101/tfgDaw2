<?php

namespace App\DTOs\Juego;

use App\DTOs\Dto;
use App\Models\Juego;

class JuegoDto extends Dto
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $description
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description
        ];
    }
}
