<?php

namespace App\Services;

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
        return $this->userRepository->all();
    }

    public function getUserById(int $id): ?UserDto
    {
        return $this->userRepository->find($id);
    }

    public function createUser(array $data): UserDto
    {
        return $this->userRepository->create($data);
    }

    public function updateUser(int $id, array $data): bool
    {
        return $this->userRepository->update($id, $data);
    }

    public function deleteUser(int $id): bool
    {
        return $this->userRepository->delete($id);
    }
}
