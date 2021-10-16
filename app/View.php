<?php

namespace App;

class View
{
    private string $fileName;
    private array $data;

    public function __construct(string $fileName, array $data = [])
    {
        $this->fileName = $fileName;
        $this->data = $data;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getData(): array
    {
        return $this->data;
    }

}