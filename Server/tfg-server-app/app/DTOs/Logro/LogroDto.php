<?php

namespace App\DTOs\Logro;

use App\DTOs\Dto;
use App\Models\Logro;

class LogroDto extends Dto
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $description,
        public ?int $juego_id
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Logro $item */
        return new self(
            id: $item->id,
            name: $item->name,
            description: $item->description,
            juego_id: $item->juego_id
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            juego_id: $data['juego_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'juego_id' => $this->juego_id
        ];
    }
}
