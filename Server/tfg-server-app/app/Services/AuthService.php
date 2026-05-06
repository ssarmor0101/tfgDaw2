<?php

namespace App\Services;

use App\DTOs\Auth\AuthDto;
use App\DTOs\User\UserDto;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class AuthService
{
    /**
     * Attempt login and return AuthDto.
     *
     * @param array $credentials
     * @return AuthDto|null
     */
    public function login(array $credentials): ?AuthDto
    {
        if (Auth::attempt($credentials)) {
            /** @var User $user */
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return new AuthDto(
                token: $token,
                user: UserDto::fromModel($user)->toArray()
            );
        }

        return null;
    }

    /**
     * Logout current user.
     *
     * @return void
     */
    public function logout(): void
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();
        }
    }
}
