<?php

namespace App\Controllers;

use App\Auth;
use App\Redirect;
use App\Services\Categories\ShowCategoriesService;
use App\Services\Products\DeleteProductService;
use App\Services\Products\EditProductService;
use App\Services\Products\SaveProductRequest;
use App\Services\Products\SaveProductService;
use App\Services\Products\SearchProductService;
use App\Services\Products\ShowProductsService;
use App\Services\Tags\FilterTagService;
use App\Services\Tags\SearchTagService;
use App\Services\Tags\ShowTagsService;
use App\View;

class ProductsController
{
    private ShowProductsService $showProductsService;
    private ShowCategoriesService $showCategoriesService;
    private ShowTagsService $showTagsService;
    private SearchProductService $searchProductService;
    private SearchTagService $searchTagService;
    private SaveProductService $saveProductService;
    private DeleteProductService $deleteProductService;
    private EditProductService $editProductService;
    private FilterTagService $filterTagService;

    public function __construct(
        ShowProductsService $showProductsService,
        ShowCategoriesService $showCategoriesService,
        ShowTagsService $showTagsService,
        SearchProductService $searchProductService,
        SearchTagService $searchTagService,
        SaveProductService $saveProductService,
        DeleteProductService $deleteProductService,
        EditProductService $editProductService,
        FilterTagService $filterTagService
    )
    {
        $this->showProductsService = $showProductsService;
        $this->showCategoriesService = $showCategoriesService;
        $this->showTagsService = $showTagsService;
        $this->searchProductService = $searchProductService;
        $this->searchTagService = $searchTagService;
        $this->saveProductService = $saveProductService;
        $this->deleteProductService = $deleteProductService;
        $this->editProductService = $editProductService;
        $this->filterTagService = $filterTagService;
    }

    public function index(): View
    {
        $products = $this->showProductsService->execute(Auth::user()->getId());
        $categories = $this->showCategoriesService->execute();
        $tags = $this->showTagsService->execute();
        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => Auth::user(),
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags()
        ]);
    }

    public function showProduct($vars): View
    {
        $product = $this->searchProductService->execute($vars["id"], Auth::user()->getId());
        $tags = $this->searchTagService->execute($product->getProductId());
        return new View("product.view.twig", [
            "product" => $product,
            "user" => Auth::user(),
            "tags" => $tags->getTags()
        ]);
    }

    public function createNewForm(): View
    {
        $categories = $this->showCategoriesService->execute();
        $tags = $this->showTagsService->execute();
        return new View("createNew.view.twig", [
            "errors" => $_SESSION["_errors"] ?? null,
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags()
        ]);
    }

    public function saveProduct(): void
    {
        $this->saveProductService->execute(
            new SaveProductRequest(
                $_POST["name"],
                $_POST["category"],
                $_POST["quantity"]), $_POST);
        Redirect::url("/products");
    }

    public function deleteProduct($vars)
    {
        if ($_POST["delete"] === "Delete") {
            $this->deleteProductService->execute($vars["id"], Auth::user()->getId());
            Redirect::url("/products");
        }
    }

    public function showEditView($vars): View
    {
        $categories = $this->showCategoriesService->execute();
        $tags = $this->showTagsService->execute();
        $product = $this->searchProductService->execute($vars["id"], Auth::user()->getId());
        return new View("productEdit.view.twig", [
            "product" => $product,
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags(),
            "errors" => $_SESSION["_errors"] ?? null
        ]);
    }

    public function editProduct($vars)
    {
        $this->editProductService->execute($vars["id"], $_POST, Auth::user()->getId());
        Redirect::url("/products");
    }

    public function showFilterView(): View
    {
        $tags = $this->showTagsService->execute();
        $selectedTags = $this->filterTagService->execute($_GET);
        $categories = $this->showCategoriesService->execute();
        $products = $this->showProductsService->execute(Auth::user()->getId(), $_GET["category"], $selectedTags);
        return new View("productsCatalog.view.twig", [
            "products" => $products->getProducts(),
            "user" => Auth::user(),
            "categories" => $categories->getCategories(),
            "tags" => $tags->getTags()
        ]);
    }
}