<?php

namespace App\Repositories;

use App\Model\Collections\TagsCollection;

interface ProductsTagsRepository
{
    public function searchByProductId(int $productId): TagsCollection;
}