<?php

namespace App\Services\Products;

use App\Auth;
use App\Model\Product;
use App\Repositories\Products\ProductsRepository;
use App\Repositories\ProductsTags\ProductsTagsRepository;
use App\Repositories\Tags\TagsRepository;
use App\Repositories\Users\MySqlUsersRepository;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class SaveProductService
{
    private ProductsRepository $productsRepository;
    private TagsRepository $tagsRepository;
    private ProductsTagsRepository $productsTagsRepository;

    public function __construct(
        ProductsRepository $productsRepository,
        TagsRepository $tagsRepository,
        ProductsTagsRepository $productsTagsRepository
    )
    {
        $this->productsRepository = $productsRepository;
        $this->tagsRepository = $tagsRepository;
        $this->productsTagsRepository = $productsTagsRepository;
    }

    public function execute(SaveProductRequest $request, array $data)
    {
        $product = new Product(
            $request->getName(),
            $request->getCategory(),
            $request->getQuantity(),
            Carbon::now()->toDateTimeString(),
            Auth::user()->getId(),
            null,
            Uuid::uuid4()->toString()
        );
        $this->productsRepository->saveProduct($product);
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