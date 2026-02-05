<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateResultadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
                // Rule::unique('resultados')->where('logro_id', $this->logro_id)->ignore($this->route('resultado')),
                Rule::unique('resultados')->where('logro_id', $this->logro_id)->ignore($this->route()->resultado),
            ],
            'logro_id' => 'required|integer|exists:logros,id',
        ];
    }
}
