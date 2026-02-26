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
        // $numeroUsuarios = User::all()->count();
        $numeroJugadoresUnicos = Puntuacion::where('juego_id', $this->juego_id)->distinct('user_id')->count('user_id');
        $numeroJugadoresConLogro Resultado::where('logro_id', $this->id)->count('user_id');
        $porcentaje = (floatval($numeroJugadoresConLogro)/floatval($numeroJugadoresUnicos))*100;
        return [
            'juego_id' => $this->juego_id,
            'name' => $this->name,
            'descr' => $this->description,
            // 'especial' => $this->juego->name . ' - ' . $this->name,
        ];
    }
}
