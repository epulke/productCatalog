<?php

namespace App;

class Container
{
    private array $container;

    public function __construct(array $container)
    {
        foreach ($container as $key => $value)
        {
            $this->add($key, $value);
        }
    }

    public function add($key, $value)
    {
        $this->container["$key"] = $value;
    }

    public function getContainer(): array
    {
        return $this->container;
    }

    public function getDependency($dependency)
    {
        return $this->container[$dependency];
    }
}