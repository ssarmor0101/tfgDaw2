<?php

namespace App\Helpers;

use App\DTOs\User\UserDto;

class AmigoHelper
{

    /**
     * Ordena dos usuarios segun su id para crear una tupla de amigos.
     * Se asume que $a != $b.
     *
     * @param UserDto $a
     * @param UserDto $b
     * @return array<UserDto>
     */
    public static function orderUsersPairForAmigo(UserDto $a, UserDto $b): array
    {
        if ($a->id > $b->id)
            return [$b, $a];
        return [$a, $b];
    }
}