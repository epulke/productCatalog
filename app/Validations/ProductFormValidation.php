<?php

namespace App\Validations;

use App\Exceptions\ProductValidationException;

class ProductFormValidation
{
    private array $errors = [];

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

        $category = ["Footwear", "Clothing", "Accessories"];
        if (!in_array($data["category"], $category))
        {
            $this->errors[] = "This is invalid category.";
        }

        if (count($this->errors) > 0) throw new ProductValidationException();

    }
}