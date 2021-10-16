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

    public function downloadProducts(?string $category = null): ProductsCollection
    {
        if (is_null($category) || $category === "all")
        {
            $statement = $this->connection->prepare("select * from products");
        } else {
            $statement = $this->connection->prepare("select * from products where category='{$category}'");
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
                $item["dateUpdated"]
            ));
        }
        return $collection;
    }

    public function searchProduct(string $name): Product
    {
        $statement = $this->connection->prepare("select * from products where name='{$name}'");
        $statement->execute();
        $item = $statement->fetch();

        return new Product(
            $item["name"],
            $item["category"],
            $item["quantity"],
            $item["dateAdded"],
            $item["dateUpdated"]
        );
    }

    public function saveProduct(Product $product): void
    {
        $statement = $this->connection->prepare("insert into products (
                      name, 
                      category, 
                      quantity, 
                      dateAdded
                      ) values (
                                :name, 
                                :category, 
                                :quantity, 
                                :dateAdded
                      )");
        $statement->execute([
            ":name" => $product->getName(),
            ":category" => $product->getCategory(),
            ":quantity" => $product->getQuantity(),
            ":dateAdded" => $product->getDateAdded()
        ]);
    }

    public function deleteProduct(string $name): void
    {
        $statement = $this->connection->prepare("delete from products where name='{$name}'");
        $statement->execute();
    }

    public function editProduct(string $searchName, string $name, string $category, int $quantity, string $dateUpdated): void
    {
        $statement = $this->connection->prepare("select * from products where name='{$searchName}'");
        $statement->execute();
        $item = $statement->fetch();
        $id = $item["id"];

        $statement = $this->connection->prepare("update products set
                    name=(:name),
                    category=(:category),
                    quantity=(:quantity),
                    dateUpdated=(:dateUpdated) where id=(:id)");
        $statement->execute([
            ":name" => $name,
            ":category" => $category,
            ":quantity" => $quantity,
            ":dateUpdated" => $dateUpdated,
            ":id" => $id
        ]);
    }
}