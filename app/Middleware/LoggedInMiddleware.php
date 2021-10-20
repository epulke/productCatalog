<?php

namespace App\Middleware;

use App\Auth;
use App\Redirect;

class LoggedInMiddleware implements Middleware
{
    public function handle(): void
    {
        if (Auth::loggedIn())
        {
            Redirect::url("/products");
            exit;
        }
    }
}