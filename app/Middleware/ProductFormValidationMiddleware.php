<?php

namespace App\Middleware;

use App\Exceptions\ProductValidationException;
use App\Redirect;
use App\Validations\ProductFormValidation;

class ProductFormValidationMiddleware implements Middleware
{
    private ProductFormValidation $validator;

    public function __construct()
    {
        $this->validator = new ProductFormValidation();
    }

    public function handle(): void
    {
        try {
            $this->validator->productFieldsValidation($_POST);

        } catch (ProductValidationException $exception) {
            $_SESSION["_errors"] = $this->validator->getErrors();
            Redirect::url("/createNew");
            exit;
        }
    }
}
