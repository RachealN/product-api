<?php

namespace App\Service;
 
use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductService
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(): array
    {
        return $this->productRepository->findAllProducts();
    }

    public function store(array $data, ?Product $existingProduct = null): Product
    {

        if (!$existingProduct) {
            $product = new Product();
        } else {
            $product = $existingProduct;
        }

        $product->setName($data['name'] ?? $product->getName());
        $product->setPrice($data['price'] ?? $product->getPrice());
        $product->setStockQuantity($data['stock_quantity'] ?? $product->getStockQuantity());
        $product->setDescription($data['description'] ?? $product->getDescription());

        $this->productRepository->createOrUpdate($product);

        return $product;
    }

    public function deleteProduct(Product $product): void
    {
        $this->productRepository->deleteProduct($product);
    }
}