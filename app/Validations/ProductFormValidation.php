<?php

namespace App\Validations;

use App\Exceptions\ProductValidationException;
use App\Repositories\Categories\CategoriesRepository;
use App\Repositories\Categories\MySqlCategoriesRepository;
use DI\Container;

class ProductFormValidation
{
    private ?array $errors = [];
    private CategoriesRepository $categoriesRepository;

    public function __construct(?array $errors = [], Container $container)
    {
        $this->errors = $errors;
        $this->categoriesRepository = $container->get(MySqlCategoriesRepository::class);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function productFieldsValidation(array $data)
    {
        if (empty($data["name"]) || empty($data["category"]) || empty($data["quantity"]))
        {
            $this->errors[] = "Please fill in all fields!";
        }

        $quantity = (int) $data["quantity"];
        if ($quantity <= 0)
        {
            $this->errors[] = "Quantity should be number and it should be greater than 0.";
        }

        $category = $this->categoriesRepository->getAll()->getCategoriesArray();
        if (!in_array($data["category"], $category))
        {
            $this->errors[] = "This is invalid category.";
        }

        if (count($this->errors) > 0) throw new ProductValidationException();
    }
}