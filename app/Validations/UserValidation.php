<?php

namespace App\Validations;

use App\Exceptions\UserValidationException;
use App\Model\User;
use App\Repositories\Users\MySqlUsersRepository;
use DI\Container;

class UserValidation
{
    private array $errors = [];
    private MySqlUsersRepository $repository;

    public function __construct(Container $container,?array $errors = [])
    {
        $this->errors = $errors;
        $this->repository = $container->get(MySqlUsersRepository::class);
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function registryFieldsValidation(array $data)
    {
        if (empty($data["name"]) || empty($data["email"]) ||
            empty($data["password"]) || empty($data["password_confirm"]))
        {
            $this->errors[] = "Please fill in all the fields.";
        }

        $user = $this->repository->findByEmail($data["email"]);
        if ($user instanceof User)
        {
            $this->errors[] = "This email is already registered.";
        }

        if ($data["password"] !== $data["password_confirm"])
        {
            $this->errors[] = "The passwords do not match.";
        }

        if (count($this->errors) > 0) throw new UserValidationException();
    }

    public function logInValidation(array $data)
    {

        $user = $this->repository->findByEmail($data["email"]);

        if (is_null($user))
        {
            $this->errors[] = "This email is not registered.";
        }

        if ($user instanceof User && password_verify($data["password"], $user->getPasswordHash()) === false)
        {
            $this->errors[] = "The password is incorrect.";
        }

        if (count($this->errors) > 0) throw new UserValidationException();
    }
}