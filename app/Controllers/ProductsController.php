<?php

namespace App\Controllers;

use App\Auth;
use App\Exceptions\ProductValidationException;
use App\Model\Product;
use App\Redirect;
use App\Repositories\MySqlProductsRepository;
use App\Validations\ProductFormValidation;
use App\View;
use Carbon\Carbon;

class ProductsController
{
    private MySqlProductsRepository $repository;
    private ProductFormValidation $validator;

    public function __construct()
    {
        $this->repository = new MySqlProductsRepository();
        $this->validator = new ProductFormValidation();
    }

    public function index(): View
    {
        $products = $this->repository->downloadProducts();
        (Auth::user() !== null) ? $user = Auth::user()->getName() : $user = null;

        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => $user
            ]);
    }

    public function showProduct($vars): View
    {
        $product = $this->repository->searchProduct($vars["id"]);
        return new View("product.view.twig", ["product" => $product]);
    }

    public function createNewForm(): View
    {
        return new View("createNew.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function saveProduct(): void
    {
        try {
            $this->validator->productFieldsValidation($_POST);
            $product = new Product(
                $_POST["name"],
                $_POST["category"],
                $_POST["quantity"],
                Carbon::now()->toDateTimeString()
            );

            $this->repository->saveProduct($product);

            Redirect::url("/products");
        } catch (ProductValidationException $exception) {
            $_SESSION["_errors"] = $this->validator->getErrors();
            Redirect::url("/createNew");
            exit;
        }
    }

    public function deleteProduct($vars)
    {
        $this->repository->deleteProduct($vars["id"]);
        Redirect::url("/products");
    }

    public function showEditView($vars): View
    {
        $product = $this->repository->searchProduct($vars["id"]);
        return new View("productEdit.view.twig", [
            "product" => $product,
            "errors" => $_SESSION["_errors"] ?? null
        ]);
    }

    public function editProduct($vars)
    {
        $this->repository->searchProduct($vars["id"]);
        try {
            $this->validator->productFieldsValidation($_POST);
            $this->repository->editProduct(
                $vars["id"],
                $_POST["name"],
                $_POST["category"],
                $_POST["quantity"],
                Carbon::now()->toDateTimeString()
            );
            Redirect::url("/products");
        } catch (ProductValidationException $exception) {
            $_SESSION["_errors"] = $this->validator->getErrors();
            Redirect::url("/products/{$vars['id']}/edit");
            exit;
        }
    }

    public function showFilterView(): View
    {
        $products = $this->repository->downloadProducts($_GET["category"]);
        return new View("productsCatalog.view.twig", ["products" => $products->getProducts()]);
    }
}