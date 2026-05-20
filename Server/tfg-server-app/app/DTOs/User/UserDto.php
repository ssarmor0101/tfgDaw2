<?php

namespace App\DTOs\User;

use App\DTOs\Dto;
use App\Models\User;

class UserDto extends Dto
{
    public function __construct(
        public ?int $id = null,
        public ?string $name = null,
        public ?string $email = null,
        public ?int $rol_id = null,
        public ?string $password = null,
        public ?array $rol = null
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var User $item */
        return new self(
            id: $item->id,
            name: $item->name,
            email: $item->email,
            rol_id: $item->rol_id,
            password: $item->password,
            rol: $item->rol?->toArray(),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            email: $data['email'] ?? null,
            rol_id: $data['rol_id'] ?? null,
            password: $data['password'] ?? null,
            rol: $data['rol'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'rol_id' => $this->rol_id,
            'password' => $this->password,
            'rol' => $this->rol,
        ];
    }
}
