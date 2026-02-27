<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            // 'email' => 'required|email:rfc,filter_unicode|max:100|unique:users,email,' . $this->route('user')->id,
            'email' => [
                'required',
                'email:rfc,filter_unicode',
                'max:100',
                Rule::unique('users')->ignore($this->user)
            ],
            'use_password' => 'sometimes|boolean',
            'password' => 'sometimes|required_if:use_password,1|string|min:4|confirmed',
            'rol_id' => 'sometimes|required|exists:roles,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo debe ser una dirección válida.',
            'email.max' => 'El correo no puede tener más de :max caracteres.',
            'email.unique' => 'Ese correo ya está en uso.',

            'use_password.boolean' => 'El indicador de uso de contraseña debe ser verdadero o falso.',
            'password.required_if' => 'La contraseña es obligatoria cuando se solicita.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',

            'rol_id.required' => 'El rol es obligatorio cuando se proporciona.',
            'rol_id.exists' => 'El rol seleccionado no existe.',
        ];
    }
}
