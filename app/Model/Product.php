<?php

namespace App\Model;

class Product
{
    private string $name;
    private string $category;
    private int $quantity;
    private string $dateAdded;
    private ?string $dateUpdated = null;
    private string $userId;
    private ?string $productId;

    public function __construct(
        string $name,
        string $category,
        int $quantity,
        string $dateAdded,
        string $userId,
        ?string $dateUpdated = null,
        ?string $productId = null
    )
    {
        $this->name = $name;
        $this->category = $category;
        $this->quantity = $quantity;
        $this->dateAdded = $dateAdded;
        $this->dateUpdated = $dateUpdated;
        $this->userId = $userId;
        $this->productId = $productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getDateAdded(): string
    {
        return $this->dateAdded;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getDateUpdated(): ?string
    {
        return $this->dateUpdated;
    }

    public function getProductId(): ?string
    {
        return $this->productId;
    }

    public function setDateUpdated(string $dateUpdated): void
    {
        $this->dateUpdated = $dateUpdated;
    }
}