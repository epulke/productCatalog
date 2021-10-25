<?php

namespace App\Services\Categories;

use App\Model\Collections\CategoriesCollection;
use App\Repositories\Categories\CategoriesRepository;

class ShowCategoriesService
{
    private CategoriesRepository $repository;

    public function __construct(CategoriesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): CategoriesCollection
    {
        return $this->repository->getAll();
    }


}