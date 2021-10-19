<?php

namespace App\Repositories;

use App\Model\Collections\TagsCollection;

interface TagsRepository
{
    public function getTags(): TagsCollection;
}