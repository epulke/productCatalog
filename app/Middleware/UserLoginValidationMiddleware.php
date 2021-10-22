<?php

namespace App\Middleware;

use App\Exceptions\UserValidationException;
use App\Redirect;
use App\Validations\UserValidation;
use DI\Container;

class UserLoginValidationMiddleware implements Middleware
{
    private UserValidation $validation;

    public function __construct(Container $container)
    {
        $this->validation = $container->get(UserValidation::class);
    }

    public function handle(): void
    {
        try {
            $this->validation->logInValidation($_POST);
        } catch (UserValidationException $exception) {
            $_SESSION["_errors"] = $this->validation->getErrors();
            Redirect::url("/login");
        }
    }
}