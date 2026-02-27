<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Puntuacion;

class StorePuntuacionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Puntuacion::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'juego_id' => 'required|integer|exists:juegos,id',
            'puntuacion' => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.integer' => 'El usuario debe ser un número.',
            'user_id.exists' => 'El usuario no existe.',

            'juego_id.required' => 'El juego es obligatorio.',
            'juego_id.integer' => 'El juego debe ser un número.',
            'juego_id.exists' => 'El juego no existe.',

            'puntuacion.required' => 'La puntuación es obligatoria.',
            'puntuacion.integer' => 'La puntuación debe ser un número.',
            'puntuacion.min' => 'La puntuación debe ser como mínimo :min.',
        ];
    }
}
