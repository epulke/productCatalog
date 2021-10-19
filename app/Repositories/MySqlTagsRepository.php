<?php

namespace App\Repositories;

use App\Model\Collections\TagsCollection;
use App\Model\Tag;
use PDO;

class MySqlTagsRepository implements TagsRepository
{
    private PDO $connection;

    public function __construct()
    {
        $config = require "config.php";
        $this->connection = new PDO(
            $config["connection"].";dbname=".$config["name"],
            $config["username"],
            $config["password"],
            $config["options"]
        );
    }

    public function getTags(): TagsCollection
    {
        $statement = $this->connection->prepare("select * from tags");
        $statement->execute();
        $items = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new TagsCollection();

        foreach ($items as $item)
        {
            $collection->add(new Tag(
                $item["id"],
                $item["name"]
            ));
        }

        return $collection;
    }

}