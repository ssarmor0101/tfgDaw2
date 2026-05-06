<?php

namespace App\DTOs\Auth;

use App\DTOs\Dto;

class AuthDto extends Dto
{
    public function __construct(
        public ?string $token,
        public ?array $user,
    ) {
    }

    public static function fromModel($item): self
    {
        return new self(
            token: $item['token'],
            user: $item['user'],
        );
    }

    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'user' => $this->user,
        ];
    }
}
