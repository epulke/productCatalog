<?php

namespace App\Services\Tags;

use App\Model\Collections\TagsCollection;
use App\Repositories\ProductsTags\ProductsTagsRepository;

class SearchTagService
{
    private ProductsTagsRepository $repository;

    public function __construct(ProductsTagsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $productId): TagsCollection
    {
        return $this->repository->searchByProductId($productId);
    }
}