<?php

use Psr\Container\ContainerInterface;

$config = require "config.php";
$db = $config["connection"].";dbname=".$config["name"];
$user = $config["username"];
$password = $config["password"];
$options = $config["options"];

$connection = [
    "PDO" => function (ContainerInterface $c) {
    return new PDO ($c->get());
    }
];