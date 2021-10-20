<?php

namespace App;

use App\Model\User;
use App\Repositories\MySqlUsersRepository;

class Auth
{
    private static ?MySqlUsersRepository $repository = null;
    private static ?User $authUser = null;

    public static function loggedIn(): bool
    {
        return isset($_SESSION["userId"]);
    }

    public static function user(): ?User
    {

        if (!self::loggedIn()) return null;

        if (self::$repository === null)
        {
            self::$repository = new MySqlUsersRepository();
        }

        if (self::$authUser === null)
        {
            self::$authUser = self::$repository->findById($_SESSION["userId"]);
        }

        return self::$authUser;
    }

    public static function logOut(): void
    {
        unset($_SESSION["userId"]);
        if (self::$authUser !== null) self::$authUser = null;
    }
}