<?php

use App\Repositories\Categories\CategoriesRepository;
use App\Repositories\Categories\MySqlCategoriesRepository;
use App\Repositories\Products\MySqlProductsRepository;
use App\Repositories\Products\ProductsRepository;
use App\Repositories\ProductsTags\MySqlProductsTagsRepository;
use App\Repositories\ProductsTags\ProductsTagsRepository;
use App\Repositories\Tags\MySqlTagsRepository;
use App\Repositories\Tags\TagsRepository;
use App\Repositories\Users\MySqlUsersRepository;
use App\Repositories\Users\UsersRepository;
use App\View;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
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
//$container->set(PDO::class, DI\factory(function () {
//    $config = require "config.php";
//    $dsn = $config["connection"].";dbname=".$config["name"];
//    return new PDO($dsn, $config["username"], $config["password"], $config["options"]);
//}));
//var_dump($container->get(PDO::class));
//$container->set(UsersRepository::class, DI\factory(function (ContainerInterface $c) {
//    var_dump("I was here");
//    return new MySqlUsersRepository($c->get(PDO::class));
//}));
$container->set(UsersRepository::class, DI\create(MySqlUsersRepository::class));
$container->set(ProductsRepository::class, DI\create(MySqlProductsRepository::class));
$container->set(TagsRepository::class, DI\create(MySqlTagsRepository::class));
$container->set(ProductsTagsRepository::class, DI\create(MySqlProductsTagsRepository::class));
$container->set(CategoriesRepository::class, DI\create(MySqlCategoriesRepository::class));



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

