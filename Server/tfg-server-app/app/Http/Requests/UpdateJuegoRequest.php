<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Juego;

class UpdateJuegoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('juego'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:juegos,name,' . $this->route('juego')->id,
            'description' => 'required|string|max:250'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del juego es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',
            'name.unique' => 'El nombre del juego ya existe.',

            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser texto.',
            'description.max' => 'La descripción no puede tener más de :max caracteres.',
        ];
    }
}
