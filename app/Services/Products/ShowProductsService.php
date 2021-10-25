<?php

namespace App\Services\Products;

use App\Model\Collections\ProductsCollection;
use App\Repositories\Products\ProductsRepository;

class ShowProductsService
{
    private ProductsRepository $repository;

    public function __construct(ProductsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $userId, ?string $category = null, ?array $tag = []): ProductsCollection
    {
        return $this->repository->downloadProducts($userId, $category, $tag);
    }

}