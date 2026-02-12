<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'juego_id' => $this->juego_id,
            'name' => $this->name,
            'descr' => $this->description,
            // 'especial' => $this->juego->name . ' - ' . $this->name,
        ];
    }
}
