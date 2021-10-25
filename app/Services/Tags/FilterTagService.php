<?php

namespace App\Services\Tags;

use App\Repositories\Tags\TagsRepository;

class FilterTagService
{
    private TagsRepository $repository;

    public function __construct(TagsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): array
    {
        $tags = $this->repository->getTags();
        $selectedTags = [];
        foreach ($tags->getTags() as $tag) {
            if (in_array((string)$tag->getId(), $data)) {
                $selectedTags[] = $tag->getId();
            }
        }
        return $selectedTags;
    }
}