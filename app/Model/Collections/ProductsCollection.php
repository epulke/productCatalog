<?php

namespace App\Model\Collections;

use App\Model\Product;

class ProductsCollection
{
    private array $products = [];

    public function __construct(array $products = [])
    {
        foreach ($products as $product)
        {
            if($product instanceof Product) $this->addProduct($product);
        }
    }

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }

    public function getProducts(): array
    {
        return $this->products;
    }
}
