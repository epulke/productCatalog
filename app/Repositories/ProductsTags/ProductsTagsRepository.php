<?php

namespace App\Repositories\ProductsTags;

use App\Model\Collections\TagsCollection;
use App\Model\Product;

interface ProductsTagsRepository
{
    public function searchByProductId(string $productId): TagsCollection;
    public function saveProductsTags(Product $product, array $tags): void;
    public function delete(Product $product): void;
}