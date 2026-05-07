<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\DTOs\User\UserDto;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function getAllUsers(): Collection
    {
        return UserDto::collection($this->userRepository->all());
    }

    public function getUserById(int $id): ?UserDto
    {
        $user = $this->userRepository->find($id);
        return $user ? UserDto::fromModel($user) : null;
    }

    public function createUser(array $data): UserDto
    {
        $userDto = UserDto::fromArray($data);
        $user = $this->userRepository->create($userDto);
        return UserDto::fromModel($user);
    }

    public function updateUser(int $id, array $data): bool
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return false;
        }
        $userDto = UserDto::fromArray($data);
        return $this->userRepository->update($user, $userDto);
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return false;
        }
        return $this->userRepository->delete($user);
    }
}
