<?php

namespace App\Repositories\Categories;

use App\Model\Collections\CategoriesCollection;

interface CategoriesRepository
{
    public function getAll(): CategoriesCollection;
}