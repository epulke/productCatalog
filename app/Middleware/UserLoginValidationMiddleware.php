<?php

namespace App\Middleware;

use App\Exceptions\UserValidationException;
use App\Redirect;
use App\Validations\UserValidation;

class UserLoginValidationMiddleware implements Middleware
{
    private UserValidation $validation;

    public function __construct()
    {
        $this->validation = new UserValidation();
    }

    public function handle(): void
    {
        try {
            $this->validation->logInValidation($_POST);
        } catch (UserValidationException $exception) {
            $_SESSION["_errors"] = $this->validation->getErrors();
            Redirect::url("/login");
            exit;
        }
    }
}