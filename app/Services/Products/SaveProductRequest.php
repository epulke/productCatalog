<?php

namespace App\Services\Products;

class SaveProductRequest
{
    private string $name;
    private string $category;
    private int $quantity;

    public function __construct(string $name, string $category, int $quantity)
    {
        $this->name = $name;
        $this->category = $category;
        $this->quantity = $quantity;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

}