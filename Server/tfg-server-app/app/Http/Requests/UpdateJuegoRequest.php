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
        return $this->user()->can('update', Juego::class);
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
            'name.unique' => 'El nombre del juego ya existe',
        ];
    }
}
