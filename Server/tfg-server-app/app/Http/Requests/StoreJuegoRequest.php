<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Juego;

class StoreJuegoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()->isAdmin();

        return $this->user()->can('create', Juego::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|unique:App\Models\Juego,name|string|max:50',
            'description' => 'required|string|max:250'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del juego es obligatorio.',
            'name.unique' => 'Ya existe un juego con ese nombre.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser texto.',
            'description.max' => 'La descripción no puede tener más de :max caracteres.',
        ];
    }
}
