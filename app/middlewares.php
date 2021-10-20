<?php

use App\Middleware\LoggedInMiddleware;
use App\Middleware\AuthorizedMiddleware;

return [
    'ProductsController@index' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@showProduct' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@saveProduct' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@createNewForm' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@deleteProduct' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@showEditView' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@editProduct' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@showFilterView' => [
        AuthorizedMiddleware::class
    ],

    'UsersController@registerForm' => [
        LoggedInMiddleware::class
    ],

    'UsersController@registerUser' => [
        LoggedInMiddleware::class
    ],

    'UsersController@logInView' => [
        LoggedInMiddleware::class
    ],

    'UsersController@logInUser' => [
        LoggedInMiddleware::class
    ],

    'UsersController@userInfo' => [
        AuthorizedMiddleware::class
    ],

    'UsersController@userLogOut' => [
        AuthorizedMiddleware::class
    ]
];