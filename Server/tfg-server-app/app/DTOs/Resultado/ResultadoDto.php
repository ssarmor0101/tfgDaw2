<?php

namespace App\DTOs\Resultado;

use App\DTOs\Dto;
use App\Models\Resultado;

class ResultadoDto extends Dto
{
    public function __construct(
        public ?int $id,
        public ?int $user_id,
        public ?int $logro_id
    ) {
    }

    public static function fromModel($item): self
    {
        /** @var Resultado $item */
        return new self(
            id: $item->id,
            user_id: $item->user_id,
            logro_id: $item->logro_id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'logro_id' => $this->logro_id
        ];
    }
}
