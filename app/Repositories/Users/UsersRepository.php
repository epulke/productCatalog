<?php

namespace App\Repositories\Users;

use App\Model\User;

interface UsersRepository
{
    public function findByEmail(string $email): ?User;
    public function findById(string $id): User;
    public function addUser(User $user);
}