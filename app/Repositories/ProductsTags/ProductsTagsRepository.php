<?php

namespace App\Repositories\ProductsTags;

use App\Model\Collections\TagsCollection;

interface ProductsTagsRepository
{
    public function searchByProductId(string $productId): TagsCollection;
}