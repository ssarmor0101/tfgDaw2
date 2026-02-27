<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Amigo;

class StoreAmigoRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('create', Amigo::class);
    }

    public function rules()
    {
        return [
            'user_id' => ['required','integer','exists:users,id'],
            'friend_id' => ['required','integer','exists:users,id','different:user_id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $a = (int) $this->input('user_id');
            $b = (int) $this->input('friend_id');
            if ($a && $b) {
                if (Amigo::pairExists($a, $b)) {
                    $validator->errors()->add('friend_id', 'La relación de amistad ya existe.');
                }
            }
        });
    }

    public function messages()
    {
        return [
            'user_id.required' => 'El usuario A es obligatorio.',
            'user_id.integer' => 'El usuario A debe ser un número.',
            'user_id.exists' => 'El usuario A no existe.',

            'friend_id.required' => 'El usuario B es obligatorio.',
            'friend_id.integer' => 'El usuario B debe ser un número.',
            'friend_id.exists' => 'El usuario B no existe.',
            'friend_id.different' => 'Los dos usuarios deben ser distintos.',
        ];
    }
}
