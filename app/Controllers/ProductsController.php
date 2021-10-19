<?php

namespace App\Controllers;

use App\Auth;
use App\Exceptions\ProductValidationException;
use App\Model\Product;
use App\Redirect;
use App\Repositories\MySqlProductsRepository;
use App\Repositories\MySqlProductsTagsRepository;
use App\Repositories\MySqlTagsRepository;
use App\Validations\ProductFormValidation;
use App\View;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class ProductsController
{
    private MySqlProductsRepository $productsRepository;
    private ProductFormValidation $validator;
    private MySqlProductsTagsRepository $productsTagsRepository;
    private MySqlTagsRepository $tagsRepository;

    public function __construct()
    {
        $this->productsRepository = new MySqlProductsRepository();
        $this->validator = new ProductFormValidation();
        $this->productsTagsRepository = new MySqlProductsTagsRepository();
        $this->tagsRepository = new MySqlTagsRepository();
    }

    public function index(): View
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;

        if ($user === null)
        {
            Redirect::url("/login");
        }
        $products = $this->productsRepository->downloadProducts(Auth::user()->getId());

        $tags = $this->tagsRepository->getTags();
        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => $user,
            "tags" => $tags->getTags()
            ]);
    }

    public function showProduct($vars): View
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        $product = $this->productsRepository->searchProduct($vars["id"], $user->getId());
        $tags = $this->productsTagsRepository->searchByProductId($product->getProductId());
        return new View("product.view.twig", [
            "product" => $product,
            "user" => $user,
            "tags" => $tags->getTags()
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
                $user->getId(),
                null,
                Uuid::uuid4()->toString()
            );

            $this->productsRepository->saveProduct($product);

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
            $this->productsRepository->deleteProduct($vars["id"], $user->getId());
            Redirect::url("/products");
        }
    }

    public function showEditView($vars): View
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        $product = $this->productsRepository->searchProduct($vars["id"], $user->getId());
        return new View("productEdit.view.twig", [
            "product" => $product,
            "errors" => $_SESSION["_errors"] ?? null
        ]);
    }

    public function editProduct($vars)
    {
        (Auth::user() !== null) ? $user = Auth::user() : $user = null;
        $this->productsRepository->searchProduct($vars["id"], $user->getId());
        try {
            $this->validator->productFieldsValidation($_POST);
            $this->productsRepository->editProduct($vars["id"], $_POST, $user->getId());
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
        $tags = $this->tagsRepository->getTags();
        $selectedTags = [];
        foreach($tags->getTags() as $tag)
        {
            if (in_array((string)$tag->getId(), $_GET))
            {
                $selectedTags[] = $tag->getId();
            }
        }

        $products = $this->productsRepository->downloadProducts($user->getId() ,$_GET["category"], $selectedTags);
        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => $user,
            "tags" => $tags->getTags()
        ]);
    }
}