<?php

namespace App\Repositories\ProductsTags;

use App\Model\Collections\TagsCollection;
use App\Model\Product;
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

    public function searchByProductId(string $productId): TagsCollection
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

    public function saveProductsTags(Product $product, array $tags)
    {
        foreach ($tags as $tag)
        {
            $statement = $this->connection->prepare(
                "insert into productsTags (productId, tagId) 
                    values (:productId, :tagId)");
            $statement->execute([
                ":productId" => $product->getProductId(),
                ":tagId" => $tag
            ]);
        }

    }
}