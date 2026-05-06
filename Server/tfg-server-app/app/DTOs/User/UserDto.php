<?php

namespace App\DTOs\User;

use App\DTOs\Dto;
use App\Models\User;

class UserDto extends Dto
{
    public function __construct(
        public ?int $id,
        public ?string $name,
        public ?string $email,
        public ?int $rol_id
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var User $item */
        return new self(
            id: $item->id,
            name: $item->name,
            email: $item->email,
            rol_id: $item->rol_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'rol_id' => $this->rol_id
        ];
    }
}
