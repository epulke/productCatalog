<?php

namespace App\Controllers;

use App\Auth;
use App\Model\User;
use App\Redirect;
use App\Repositories\Users\MySqlUsersRepository;
use App\Repositories\Users\UsersRepository;
use App\View;
use Ramsey\Uuid\Uuid;
use DI\Container;

class UsersController
{
    private UsersRepository $repository;

    public function __construct(Container $container)
    {
        $this->repository = $container->get(MySqlUsersRepository::class);
    }

    public function registerForm(): View
    {
        return new View("register.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function registerUser()
    {
        $user = new User(
            Uuid::uuid4()->toString(),
            $_POST["name"],
            $_POST["email"],
            password_hash($_POST["password"], PASSWORD_DEFAULT)
        );
        $this->repository->addUser($user);
        Redirect::url("/login");
    }

    public function logInView(): View
    {
        return new View("logIn.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function logInUser()
    {
        $user = $this->repository->findByEmail($_POST["email"]);
        $_SESSION["userId"] = $user->getId();
        Redirect::url("/products");
    }

    public function userInfo(): View
    {
        return new View("userInfo.twig", ["user" => Auth::user()]);
    }

    public function userLogOut()
    {
        Auth::logOut();
        Redirect::url("/login");
    }
}