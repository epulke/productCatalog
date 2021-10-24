<?php

namespace App\Controllers;

use App\Auth;
use App\Model\Product;
use App\Redirect;
use App\Repositories\Categories\CategoriesRepository;
use App\Repositories\Categories\MySqlCategoriesRepository;
use App\Repositories\Products\MySqlProductsRepository;
use App\Repositories\ProductsTags\MySqlProductsTagsRepository;
use App\Repositories\Tags\MySqlTagsRepository;
use App\Repositories\Products\ProductsRepository;
use App\Repositories\ProductsTags\ProductsTagsRepository;
use App\Repositories\Tags\TagsRepository;
use App\View;
use Carbon\Carbon;
use DI\Container;
use Ramsey\Uuid\Uuid;

class ProductsController
{
    private ProductsRepository $productsRepository;
    private ProductsTagsRepository $productsTagsRepository;
    private TagsRepository $tagsRepository;
    private CategoriesRepository $categoriesRepository;

    public function __construct(Container $container)
    {
        $this->productsRepository = $container->get(MySqlProductsRepository::class);
        $this->productsTagsRepository = $container->get(MySqlProductsTagsRepository::class);
        $this->tagsRepository = $container->get(MySqlTagsRepository::class);
        $this->categoriesRepository = $container->get(MySqlCategoriesRepository::class);
    }

    public function index(): View
    {
        $products = $this->productsRepository->downloadProducts(Auth::user()->getId());
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getTags();
        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => Auth::user(),
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags()
        ]);
    }

    public function showProduct($vars): View
    {
        $product = $this->productsRepository->searchProduct($vars["id"], Auth::user()->getId());
        $tags = $this->productsTagsRepository->searchByProductId($product->getProductId());
        return new View("product.view.twig", [
            "product" => $product,
            "user" => Auth::user(),
            "tags" => $tags->getTags()
        ]);
    }

    public function createNewForm(): View
    {
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getTags();
        return new View("createNew.view.twig", [
            "errors" => $_SESSION["_errors"] ?? null,
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags()
        ]);
    }

    public function saveProduct(): void
    {
        $product = new Product(
            $_POST["name"],
            $_POST["category"],
            $_POST["quantity"],
            Carbon::now()->toDateTimeString(),
            Auth::user()->getId(),
            null,
            Uuid::uuid4()->toString()
        );
        $this->productsRepository->saveProduct($product);

        $tags = $this->tagsRepository->getTags();
        $selectedTags = [];
        foreach ($tags->getTags() as $tag) {
            if (in_array((string)$tag->getId(), $_POST)) {
                $selectedTags[] = $tag->getId();
            }
        }
        $this->productsTagsRepository->saveProductsTags($product, $selectedTags);
        Redirect::url("/products");
    }

    public function deleteProduct($vars)
    {
        if ($_POST["delete"] === "Delete") {
            $product = $this->productsRepository->searchProduct($vars["id"], Auth::user()->getId());
            $this->productsTagsRepository->delete($product);
            $this->productsRepository->deleteProduct($vars["id"], Auth::user()->getId());
            Redirect::url("/products");
        }
    }

    public function showEditView($vars): View
    {
        $categories = $this->categoriesRepository->getAll();
        $tags = $this->tagsRepository->getTags();
        $product = $this->productsRepository->searchProduct($vars["id"], Auth::user()->getId());
        return new View("productEdit.view.twig", [
            "product" => $product,
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags(),
            "errors" => $_SESSION["_errors"] ?? null
        ]);
    }

    public function editProduct($vars)
    {
        $product = $this->productsRepository->searchProduct($vars["id"], Auth::user()->getId());
        $this->productsTagsRepository->delete($product);
        $this->productsRepository->editProduct($vars["id"], $_POST, Auth::user()->getId());
        $tags = $this->tagsRepository->getTags();
        $selectedTags = [];
        foreach ($tags->getTags() as $tag) {
            if (in_array((string)$tag->getId(), $_POST)) {
                $selectedTags[] = $tag->getId();
            }
        }
        $this->productsTagsRepository->saveProductsTags($product, $selectedTags);
        Redirect::url("/products");
    }

    public function showFilterView(): View
    {
        $tags = $this->tagsRepository->getTags();
        $selectedTags = [];
        foreach ($tags->getTags() as $tag) {
            if (in_array((string)$tag->getId(), $_GET)) {
                $selectedTags[] = $tag->getId();
            }
        }
        $categories = $this->categoriesRepository->getAll();
        $products = $this->productsRepository->downloadProducts(Auth::user()->getId(), $_GET["category"], $selectedTags);
        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => Auth::user(),
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags()
        ]);
    }
}