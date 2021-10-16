<?php

namespace App\Controllers;

use App\Auth;
use App\Exceptions\UserValidationException;
use App\Model\User;
use App\Redirect;
use App\Repositories\MySqlUsersRepository;
use App\Validations\UserValidation;
use App\View;
use Ramsey\Uuid\Uuid;

class UsersController
{
    private MySqlUsersRepository $repository;
    private UserValidation $validation;

    public function __construct()
    {
        $this->repository = new MySqlUsersRepository();
        $this->validation = new UserValidation();
    }

    public function registerForm(): View
    {
        return new View("register.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function registerUser()
    {
        try {
            $this->validation->registryFieldsValidation($_POST);
            $user = new User(
                Uuid::uuid4()->toString(),
                $_POST["name"],
                $_POST["email"],
                password_hash($_POST["password"], PASSWORD_DEFAULT)
            );
            $this->repository->addUser($user);
            Redirect::url("/login");
        } catch (UserValidationException $exception){
            $_SESSION["_errors"] = $this->validation->getErrors();
            Redirect::url("/register");
            exit;
        }
    }

    public function logInView(): View
    {
        return new View("logIn.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function logInUser()
    {
        try {
            $this->validation->logInValidation($_POST);
            $user = $this->repository->findByEmail($_POST["email"]);
            $_SESSION["userId"] = $user->getId();
            Redirect::url("/products");
        } catch (UserValidationException $exception) {
            $_SESSION["_errors"] = $this->validation->getErrors();
            Redirect::url("/login");
            exit;
        }
    }

    public function userInfo(): View
    {
        $user = Auth::user();
        return new View("userInfo.twig", ["user" => $user]);
    }

    public function userLogOut()
    {
        unset($_SESSION["userId"]);
        Auth::unset();
        Redirect::url("/login");
    }
}