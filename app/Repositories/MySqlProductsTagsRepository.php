<?php

namespace App\Repositories;

use App\Model\Collections\TagsCollection;
use App\Model\Tag;
use PDO;

class MySqlProductsTagsRepository implements ProductsTagsRepository
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

    public function searchByProductId(int $productId): TagsCollection
    {
        $statement = $this->connection->prepare(
            "select * from productsTags p left join tags t on p.tagId=t.id where p.productId='{$productId}'"
        );
        $statement->execute();
        $items = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new TagsCollection();

        foreach ($items as $item)
        {
            $collection->add(
                new Tag(
                    $item["tagId"],
                    $item["name"]
                )
            );
        }
        return $collection;
    }
}