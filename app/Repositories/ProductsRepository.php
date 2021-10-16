<?php

namespace App\Repositories;

use App\Model\Collections\ProductsCollection;
use App\Model\Product;

interface ProductsRepository
{
    public function downloadProducts(?string $category): ProductsCollection;
    public function searchProduct(string $name): Product;
    public function saveProduct(Product $product): void;
    public function deleteProduct(string $name): void;
    public function editProduct(string $searchName, string $name, string $category, int $quantity, string $dateUpdated): void;
}