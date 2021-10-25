<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\Users\LoginUserService;
use App\Services\Users\RegisterUserRequest;
use App\Services\Users\RegisterUserService;
use App\View;

class UsersController
{
    private RegisterUserService $registerService;
    private LoginUserService $loginService;

    public function __construct(RegisterUserService $registerService, LoginUserService $loginService)
    {
        $this->registerService = $registerService;
        $this->loginService = $loginService;
    }

    public function registerForm(): View
    {
        return new View("register.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function registerUser()
    {
        $this->registerService->execute(new RegisterUserRequest($_POST["name"], $_POST["email"], $_POST["password"]));
        Redirect::url("/login");
    }

    public function logInView(): View
    {
        return new View("logIn.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function logInUser()
    {
        $user = $this->loginService->execute($_POST["email"]);
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