<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductsController extends AbstractController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/products', name: 'products')]
    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return $this->json($products);
    }

    #[Route('/product/create', name: 'product_create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = $this->productService->store($data);

        $violations = $validator->validate($product);

        if(count($violations) > 0){
            return $this->json($violations);
        }

        return $this->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    #[Route('/product/{id}', name: 'update_product', methods: ['PUT'])]
    public function update(Request $request, Product $product): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = $this->productService->store($data, $product);

        return $this->json(['message' => 'Product updated successfully', 'product' => $product], 200);
    }

    #[Route('/product/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return $this->json(['message' => 'Product deleted successfully'], 200);
    }
}
