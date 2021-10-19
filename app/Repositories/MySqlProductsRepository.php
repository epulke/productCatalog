<?php

namespace App\Repositories;

use App\Model\Collections\ProductsCollection;
use App\Model\Product;
use Carbon\Carbon;
use PDO;

class MySqlProductsRepository implements ProductsRepository
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

    public function downloadProducts(string $userId, ?string $category = null, ?array $tag = []): ProductsCollection
    {
        if (is_null($category) || $category === "all" && $tag === [])
        {
            $statement = $this->connection->prepare("select * from products where userId='{$userId}'");
        } elseif (!empty($tag)) {
            $s = "";
            for ($i = 0; $i < count($tag); $i++)
            {
                $s .= " t.tagId='{$tag[$i]}' or";
            }
            $s = substr($s, 0, strlen($s)-3);
            if ($category === "all")
            {
                $query = "select distinct 
                p.name, p.category, p.quantity, p.dateAdded, p.dateUpdated, p.userId, p.productId 
                from products p left join productsTags t on p.productId=t.productId 
                where p.userId='{$userId}' and (" . $s . ")";
            } else {
                $query = "select distinct 
                p.name, p.category, p.quantity, p.dateAdded, p.dateUpdated, p.userId, p.productId 
                from products p left join productsTags t on p.productId=t.productId 
                where p.userId='{$userId}' and p.category='{$category}' and (" . $s . ")";
            }
            $statement = $this->connection->prepare($query);
        } else {
            $statement = $this->connection->prepare(
                "select * from products where category='{$category}' and userId='{$userId}'"
            );
        }

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new ProductsCollection();

        foreach ($results as $item)
        {
            $collection->addProduct(new Product(
                $item["name"],
                $item["category"],
                $item["quantity"],
                $item["dateAdded"],
                $item["userId"],
                $item["dateUpdated"],
                $item["productId"]
            ));
        }
        return $collection;
    }

    public function searchProduct(string $name, string $userId): Product
    {
        $statement = $this->connection->prepare(
            "select * from products where name='{$name}' and userId='{$userId}'"
        );
        $statement->execute();
        $item = $statement->fetch();

        return new Product(
            $item["name"],
            $item["category"],
            $item["quantity"],
            $item["dateAdded"],
            $item["userId"],
            $item["dateUpdated"],
            $item["id"]
        );
    }

    public function saveProduct(Product $product): void
    {
        $statement = $this->connection->prepare("insert into products (
                      productId,
                      name, 
                      category, 
                      quantity, 
                      dateAdded,
                      userId
                      ) values (
                                :productId,
                                :name, 
                                :category, 
                                :quantity, 
                                :dateAdded,
                                :userId
                      )");
        $statement->execute([
            ":productId" => $product->getProductId(),
            ":name" => $product->getName(),
            ":category" => $product->getCategory(),
            ":quantity" => $product->getQuantity(),
            ":dateAdded" => $product->getDateAdded(),
            ":userId" => $product->getUserId()
        ]);
    }

    public function deleteProduct(string $name, string $userId): void
    {
        $statement = $this->connection->prepare("delete from products where name='{$name}' and userId='{$userId}'");
        $statement->execute();
    }

    public function editProduct(string $searchName, array $data, string $userId): void
    {
        $statement = $this->connection->prepare("select * from products where name='{$searchName}' and userId='{$userId}'");
        $statement->execute();
        $item = $statement->fetch();
        $id = $item["id"];

        $statement = $this->connection->prepare("update products set
                    name=(:name),
                    category=(:category),
                    quantity=(:quantity),
                    dateUpdated=(:dateUpdated) where id=(:id)");
        $statement->execute([
            ":name" => $data["name"],
            ":category" => $data["category"],
            ":quantity" => $data["quantity"],
            ":dateUpdated" => Carbon::now()->toDateTimeString(),
            ":id" => $id
        ]);
    }
}