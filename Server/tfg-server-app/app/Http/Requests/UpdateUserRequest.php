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
}
