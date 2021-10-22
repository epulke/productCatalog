<?php

namespace App\Repositories\Tags;

use App\Model\Collections\TagsCollection;

interface TagsRepository
{
    public function getTags(): TagsCollection;
}