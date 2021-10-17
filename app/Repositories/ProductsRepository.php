<?php

namespace App\Repositories;

use App\Model\Collections\ProductsCollection;
use App\Model\Product;

interface ProductsRepository
{
    public function downloadProducts(string $userId, ?string $category = null): ProductsCollection;
    public function searchProduct(string $name, string $userId): Product;
    public function saveProduct(Product $product): void;
    public function deleteProduct(string $name, string $userId): void;
    public function editProduct(string $searchName, array $data, string $userId): void;
}