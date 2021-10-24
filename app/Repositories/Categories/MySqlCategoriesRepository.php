<?php

namespace App\Repositories\Categories;

use App\Model\Category;
use App\Model\Collections\CategoriesCollection;
use PDO;

class MySqlCategoriesRepository implements CategoriesRepository
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

    public function getAll(): CategoriesCollection
    {
        $statement = $this->connection->prepare("select * from categories");
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        $collection = new CategoriesCollection();

        foreach ($results as $item)
        {
            $collection->add(new Category($item["name"]));
        }
        return $collection;
    }
}