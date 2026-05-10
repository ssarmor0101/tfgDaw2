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

    public function getUserByUsername(string $username): ?UserDto
    {
        $user = $this->userRepository->findByName($username);
        return $user ? UserDto::fromModel($user) : null;
    }

    public function searchUsersByName(string $name): Collection
    {
        return UserDto::collection($this->userRepository->getByName($name));
    }

    public function createUser(UserDto $userDto): UserDto
    {
        $user = $this->userRepository->create($userDto);
        return UserDto::fromModel($user);
    }

    public function updateUser(User $user, UserDto $userDto): bool
    {
        return $this->userRepository->update($user, $userDto);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}
