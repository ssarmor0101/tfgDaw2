<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Logro;

class StoreLogroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Logro::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'juego_id' => 'required|integer|exists:juegos,id',
            'name' => 'required|string|max:50|filled',
            'description' => 'required|string|max:250|filled',
        ];
    }

    public function messages(): array
    {
        return [
            'juego_id.required' => 'El juego es obligatorio.',
            'juego_id.integer' => 'El juego debe ser un número.',
            'juego_id.exists' => 'El juego seleccionado no existe.',

            'name.required' => 'El nombre del logro es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',
            'name.filled' => 'El nombre no puede estar vacío.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser texto.',
            'description.max' => 'La descripción no puede tener más de :max caracteres.',
            'description.filled' => 'La descripción no puede estar vacía.',
        ];
    }
}
