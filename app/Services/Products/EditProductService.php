<?php

namespace App\Services\Products;

use App\Repositories\Products\ProductsRepository;
use App\Repositories\ProductsTags\ProductsTagsRepository;
use App\Repositories\Tags\TagsRepository;

class EditProductService
{
    private ProductsRepository $productsRepository;
    private ProductsTagsRepository $productsTagsRepository;
    private TagsRepository $tagsRepository;

    public function __construct(
        ProductsRepository $productsRepository,
        ProductsTagsRepository $productsTagsRepository,
        TagsRepository $tagsRepository)
    {
        $this->productsRepository = $productsRepository;
        $this->productsTagsRepository = $productsTagsRepository;
        $this->tagsRepository = $tagsRepository;
    }

    public function execute(string $productName, array $data, string $userId)
    {
        $product = $this->productsRepository->searchProduct($productName, $userId);
        $this->productsTagsRepository->delete($product);
        $this->productsRepository->editProduct($productName, $data, $userId);
        $tags = $this->tagsRepository->getTags();
        $selectedTags = [];
        foreach ($tags->getTags() as $tag) {
            if (in_array((string)$tag->getId(), $data)) {
                $selectedTags[] = $tag->getId();
            }
        }
        $this->productsTagsRepository->saveProductsTags($product, $selectedTags);
    }
}