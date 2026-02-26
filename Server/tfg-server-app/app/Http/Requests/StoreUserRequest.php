<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\User;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // $this->input()
        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email:rfc:filter_unicode|max:100|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
            'rol_id' => 'required|exists:roles,id',
        ];
    }

    

    // public function messages()
    // {
    //     return [
    //         'name' => [
    //             'required' => 'Mensaje',
    //             'max' => 'Mensaje 2'
    //         ]
    //     ];
    // }
}
