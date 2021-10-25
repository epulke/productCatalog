<?php

namespace App\Services\Users;

use App\Model\User;
use App\Repositories\Users\UsersRepository;

class LoginUserService
{
    private UsersRepository $repository;

    public function __construct(UsersRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $email): User
    {
        return $this->repository->findByEmail($email);
    }


}