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

    public function createProduct(array $data): Product
    {
        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setStockQuantity($data['stock_quantity']);
        $product->setDescription($data['description']);

        $this->productRepository->save($product);

        return $product;
    }
}