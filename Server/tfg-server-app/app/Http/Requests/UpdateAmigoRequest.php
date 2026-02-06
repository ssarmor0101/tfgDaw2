<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Amigo;

class UpdateAmigoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
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
                $min = min($a, $b);
                $max = max($a, $b);
                $current = $this->route('amigo');
                $currentId = $current instanceof Amigo ? $current->id : (int) $current;
                $exists = Amigo::where('user_id', $min)->where('friend_id', $max)->where('id', '!=', $currentId)->exists();
                if ($exists) {
                    $validator->errors()->add('friend_id', 'La relación de amistad ya existe.');
                }
            }
        });
    }
}
