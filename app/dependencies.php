<?php


use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PDO::class => function (ContainerInterface $c) {
            $config = $c->get('config.php');
            $dsn = $config["connection"].";dbname=".$config["name"];
            return new PDO($dsn, $config["username"], $config["password"], $config["options"]);
        },
    ]);
};