<?php

namespace App\Controller;

use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api', name: 'api_')]
class ProductsController extends AbstractController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    private function validateProduct(ValidatorInterface $validator, $product)
    {
        $violations = $validator->validate($product);

        if (count($violations) > 0) {
            return $this->json($violations, JsonResponse::HTTP_BAD_REQUEST);
        }

        return null;
    }

    #[Route('/products', name: 'products')]
    public function index(): JsonResponse
    {
        try {
            $products = $this->productService->getAllProducts();
            return $this->json($products, 200);

        } catch (\Throwable $e) {
            return $this->json([
                'message' => $e->getMessage(),
                'status' => 401,
            ]);
        }
    }

    #[Route('/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                throw $this->createNotFoundException('Product Not Found');
            }

            return $this->json($product);
        } catch (\Throwable $e) {

            return $this->json([
                'message' => $e->getMessage(),
                'status' => 401,
            ]);
        }
    }

    #[Route('/products/create', name: 'product_create', methods: ['POST'])]
    public function create(Request $request, ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            $product = $this->productService->store($data);

            $validationResult = $this->validateProduct($validator, $product);
            if ($validationResult !== null) {
                $errorMessage = json_decode($validationResult->getContent(), true);

                return $this->json([
                    'error' => [
                        'title' => $errorMessage['title'] ?? 'Validation Failed',
                        'detail' => $errorMessage['detail'] ?? 'Unknown error'
                    ]
                ]);
            }

            return $this->json(['message' => 'Product created successfully', 'product' => $product], 201);
        } catch (\Throwable $e) {
            return $this->json([
                'message' => 'Oops, Something Happened',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }

    #[Route('/products/{id}', name: 'update_product', methods: ['PUT'])]
    public function update(Request $request, int $id, ValidatorInterface $validator): JsonResponse
    {
        try {

            $product = $this->productService->getProductById($id);

            if (!$product) {
                throw $this->createNotFoundException('Product Not Found');
            }

            $data = json_decode($request->getContent(), true);

            $product = $this->productService->store($data, $product);

            $validationResult = $this->validateProduct($validator, $product);
            if ($validationResult !== null) {
                $errorMessage = json_decode($validationResult->getContent(), true);

                return $this->json([
                    'error' => [
                        'title' => $errorMessage['title'] ?? 'Validation Failed',
                        'detail' => $errorMessage['detail'] ?? 'Unknown error'
                    ]
                ]);
            }

            return $this->json(['message' => 'Product updated successfully', 'product' => $product], 200);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'message' => 'Product Not Found',
                'status' => 404,

            ]);
        } catch (\Throwable $e) {

            return $this->json([
                'message' => 'Oops, Something Happened',
                'detail' => $e->getMessage(),
            ]);
        }
    }

    #[Route('/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);

            if (!$product) {
                throw $this->createNotFoundException('Product Not Found');
            }

            $this->productService->deleteProduct($product);

            return new JsonResponse(['message' => 'Product deleted successfully'], 200);
        } catch (NotFoundHttpException $e) {
            return $this->json([
                'message' => 'Product Not Found',
                'status' => 404,

            ]);
        } catch (\Exception $e) {
            return $this->json([
                'message' => 'Oops, Something Happened',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }
}
