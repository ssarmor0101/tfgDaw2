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
}
