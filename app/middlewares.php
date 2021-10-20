<?php

use App\Middleware\LoggedInMiddleware;
use App\Middleware\AuthorizedMiddleware;
use App\Middleware\ProductFormValidationMiddleware;
use App\Middleware\UserLoginValidationMiddleware;
use App\Middleware\UserRegistrationValidationMiddleware;

return [
    'ProductsController@index' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@showProduct' => [
        AuthorizedMiddleware::class
    ],

    'ProductsController@saveProduct' => [
        AuthorizedMiddleware::class,
        ProductFormValidationMiddleware::class
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
        AuthorizedMiddleware::class,
        ProductFormValidationMiddleware::class
    ],

    'ProductsController@showFilterView' => [
        AuthorizedMiddleware::class
    ],

    'UsersController@registerForm' => [
        LoggedInMiddleware::class
    ],

    'UsersController@registerUser' => [
        LoggedInMiddleware::class,
        UserRegistrationValidationMiddleware::class
    ],

    'UsersController@logInView' => [
        LoggedInMiddleware::class
    ],

    'UsersController@logInUser' => [
        LoggedInMiddleware::class,
        UserLoginValidationMiddleware::class
    ],

    'UsersController@userInfo' => [
        AuthorizedMiddleware::class
    ],

    'UsersController@userLogOut' => [
        AuthorizedMiddleware::class
    ]
];