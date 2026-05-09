<?php

namespace App\DTOs\Amigo;

use App\DTOs\Dto;
use App\Models\Amigo;

class AmigoDto extends Dto
{
    public function __construct(
        public ?int $id = null,
        public ?int $user_id = null,
        public ?int $friend_id = null
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Amigo $item */
        return new self(
            id: $item->id,
            user_id: $item->user_id,
            friend_id: $item->friend_id
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            user_id: $data['user_id'] ?? null,
            friend_id: $data['friend_id'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'friend_id' => $this->friend_id
        ];
    }
}
