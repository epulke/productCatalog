<?php

namespace App\Middleware;

use App\Auth;
use App\Redirect;

class AuthorizedMiddleware implements Middleware
{
    public function handle(): void
    {
        if (!Auth::loggedIn())
        {
            Redirect::url("/login");
            exit;
        }
    }
}