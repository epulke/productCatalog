<?php

namespace App\Services\Products;

use App\Model\Product;
use App\Repositories\Products\ProductsRepository;

class SearchProductService
{
    private ProductsRepository $repository;

    public function __construct(ProductsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $name, string $userId): Product
    {
        return $this->repository->searchProduct($name, $userId);
    }


}