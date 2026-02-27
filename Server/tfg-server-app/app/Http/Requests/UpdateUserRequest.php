<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    /**
     * Strip password fields if the user isn't actually changing the password.
     * This prevents the remaining validation rules (string|min:4|confirmed)
     * from running when password is empty or not provided.
     */
    protected function prepareForValidation(): void
    {
        // if the toggle is not set or false, we don't want any password data
        if (!$this->boolean('use_password')) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }

        // also drop password if it's empty so rules like min:4 don't fire
        if ($this->filled('password') === false) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // use the route model so the policy receives both arguments
        return $this->user()->can('update', $this->route('user'));
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
            'password' => 'sometimes|nullable|required_if:use_password,1|string|min:4|confirmed',
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
