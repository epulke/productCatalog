<?php

namespace App\Model\Collections;

use App\Model\Category;

class CategoriesCollection
{
    private array $categories = [];

    public function __construct(array $categories = [])
    {
        foreach ($categories as $category)
        {
            if ($category instanceof Category)
            {
                $this->add($category);
            }
        }
    }

    public function add(Category $category)
    {
        $this->categories[] = $category;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getCategoriesArray(): array
    {
        $array = [];
        foreach ($this->getCategories() as $category)
        {
            $array[] = $category->getName();
        }
        return $array;
    }
}