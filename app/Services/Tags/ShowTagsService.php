<?php

namespace App\Services\Tags;

use App\Model\Collections\TagsCollection;
use App\Repositories\Tags\TagsRepository;

class ShowTagsService
{
    private TagsRepository $repository;

    public function __construct(TagsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): TagsCollection
    {
        return $this->repository->getTags();
    }

}