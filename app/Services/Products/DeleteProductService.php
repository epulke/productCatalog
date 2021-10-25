<?php

namespace App\Services\Products;

use App\Repositories\Products\ProductsRepository;
use App\Repositories\ProductsTags\ProductsTagsRepository;

class DeleteProductService
{
    private ProductsRepository $productsRepository;
    private ProductsTagsRepository $productsTagsRepository;

    public function __construct(ProductsRepository $productsRepository, ProductsTagsRepository $productsTagsRepository)
    {
        $this->productsRepository = $productsRepository;
        $this->productsTagsRepository = $productsTagsRepository;
    }

    public function execute(string $name, string $userId)
    {
        $product = $this->productsRepository->searchProduct($name, $userId);
        $this->productsTagsRepository->delete($product);
        $this->productsRepository->deleteProduct($name, $userId);
    }
}