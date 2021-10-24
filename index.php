<?php

use App\View;
use DI\Container;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once "vendor/autoload.php";

session_start();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/products', 'ProductsController@index');

    $r->addRoute('GET', '/pleaseLogIn', 'ProductsController@pleaseView');

    $r->addRoute('GET', '/createNew', 'ProductsController@createNewForm');
    $r->addRoute('POST', '/createNew', 'ProductsController@saveProduct');

    $r->addRoute('GET', '/products/{id}', 'ProductsController@showProduct');
    $r->addRoute('POST', '/products/{id}', 'ProductsController@deleteProduct');

    $r->addRoute('GET', '/products/{id}/edit', 'ProductsController@showEditView');
    $r->addRoute('POST', '/products/{id}/edit', 'ProductsController@editProduct');

    $r->addRoute('GET', '/filter', 'ProductsController@showFilterView');

    $r->addRoute('GET', '/register', 'UsersController@registerForm');
    $r->addRoute('POST', '/register', 'UsersController@registerUser');

    $r->addRoute('GET', '/login', 'UsersController@logInView');
    $r->addRoute('POST', '/login', 'UsersController@logInUser');

    $r->addRoute('GET', '/user', 'UsersController@userInfo');
    $r->addRoute('POST', '/user', 'UsersController@userLogOut');


});

$twig = new Environment(new FilesystemLoader("app/View"), []);

$container = new Container();

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $middlewares = require "app/middlewares.php";

        if (isset($middlewares[$handler]))
        {
            foreach ($middlewares[$handler] as $middleware)
            {
                $middleware = $container->get($middleware);
                $middleware->handle();
            }
        }

        [$handler, $method] = explode("@", $handler);

        $path = "App\Controllers\\" . $handler;
        $controller = $container->get($path);
        $response = $controller->$method($vars);

        if ($response instanceof View)
        {
            /** @var View $response */
            echo $twig->render($response->getFileName(), $response->getData());
        }

        break;
}

unset($_SESSION["_errors"]);

