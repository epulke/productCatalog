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
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;

        if ($user === null)
        {
            Redirect::url("/pleaseLogIn");
        }
        $products = $this->repository->downloadProducts(Auth::user()->getId());

        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => $user
            ]);
    }

    public function showProduct($vars): View
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        $product = $this->repository->searchProduct($vars["id"], $user->getId());
        return new View("product.view.twig", [
            "product" => $product,
            "user" => $user
        ]);
    }

    public function createNewForm(): View
    {
        return new View("createNew.view.twig", ["errors" => $_SESSION["_errors"] ?? null]);
    }

    public function saveProduct(): void
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;

        try {
            $this->validator->productFieldsValidation($_POST);
            $product = new Product(
                $_POST["name"],
                $_POST["category"],
                $_POST["quantity"],
                Carbon::now()->toDateTimeString(),
                $user->getId()
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
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        if ($_POST["delete"] === "Delete")
        {
            $this->repository->deleteProduct($vars["id"], $user->getId());
            Redirect::url("/products");
        }
    }

    public function showEditView($vars): View
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        $product = $this->repository->searchProduct($vars["id"], $user->getId());
        return new View("productEdit.view.twig", [
            "product" => $product,
            "errors" => $_SESSION["_errors"] ?? null
        ]);
    }

    public function editProduct($vars)
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        $this->repository->searchProduct($vars["id"], $user->getId());
        try {
            $this->validator->productFieldsValidation($_POST);
            $this->repository->editProduct($vars["id"], $_POST, $user->getId());
            Redirect::url("/products");
        } catch (ProductValidationException $exception) {
            $_SESSION["_errors"] = $this->validator->getErrors();
            Redirect::url("/products/{$vars['id']}/edit");
            exit;
        }
    }

    public function showFilterView(): View
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        $products = $this->repository->downloadProducts($user->getId() ,$_GET["category"]);
        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => $user
        ]);
    }

    public function pleaseView(): View
    {
//        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
            return new View("pleaseLogIn.view.twig", []);
    }
}