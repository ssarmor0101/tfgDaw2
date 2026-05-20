<?php

namespace App\DTOs\Resultado;

use App\DTOs\Dto;
use App\Models\Resultado;

class ResultadoDto extends Dto
{
    public function __construct(
        public ?int $id = null,
        public ?int $user_id = null,
        public ?int $logro_id = null,
        public ?array $user = null,
        public ?array $logro = null,
        public ?array $juego = null
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Resultado $item */
        return new self(
            id: $item->id,
            user_id: $item->user_id,
            logro_id: $item->logro_id,
            user: $item->user?->toArray(),
            logro: $item->logro?->toArray(),
            juego: $item->logro?->juego?->toArray(),
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            user_id: $data['user_id'] ?? null,
            logro_id: $data['logro_id'] ?? null,
            user: $data['user'] ?? null,
            logro: $data['logro'] ?? null,
            juego: $data['juego'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'logro_id' => $this->logro_id,
            'user' => $this->user,
            'logro' => $this->logro,
            'juego' => $this->juego,
        ];
    }
}
