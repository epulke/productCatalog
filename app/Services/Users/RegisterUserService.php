<?php

namespace App\Services\Users;

use App\Model\User;
use App\Repositories\Users\UsersRepository;
use Ramsey\Uuid\Uuid;

class RegisterUserService
{
    private UsersRepository $repository;

    public function __construct(UsersRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(RegisterUserRequest $userData): void
    {
        $user = new User(
            Uuid::uuid4()->toString(),
            $userData->getName(),
            $userData->getEmail(),
            password_hash($userData->getPassword(), PASSWORD_DEFAULT)
        );
        $this->repository->addUser($user);
    }


}