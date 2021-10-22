<?php

namespace App\Repositories\Users;

use App\Model\User;
use PDO;

class MySqlUsersRepository implements UsersRepository
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

    public function findByEmail(string $email): ?User
    {
        $statement = $this->connection->prepare("select * from users where email='{$email}'");
        $statement->execute();
        $item = $statement->fetch();

        if ($item === false)
        {
            return null;
        }

        return new User(
            $item["id"],
            $item["name"],
            $item["email"],
            $item["password"]
        );
    }

    public function addUser(User $user)
    {
        $statement = $this->connection->prepare("insert into users (
                      id, 
                      name, 
                      email, 
                      password
                      ) values (
                                :id, 
                                :name, 
                                :email, 
                                :password
                      )");
        $statement->execute([
            ":id" => $user->getId(),
            ":name" => $user->getName(),
            ":email" => $user->getEmail(),
            ":password" => $user->getPasswordHash()
        ]);
    }

    public function findById(string $id): User
    {
        $statement = $this->connection->prepare("select * from users where id='{$id}'");
        $statement->execute();
        $item = $statement->fetch();

        return new User(
            $item["id"],
            $item["name"],
            $item["email"],
            $item["password"]
        );
    }
}