<?php

namespace App\Tests;

use App\Entity\Product;
use App\Service\ProductService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    private $productRepository;
    private $productService;
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->productService = new ProductService($this->productRepository);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }

    public function test_get_all_products()
    {
        $product1 = new Product();
        $product1->setName('Dress');
        $product1->setPrice('9000');
        $product1->setStockQuantity(5);
        $product1->setDescription('My new dress');

        $product2 = new Product();
        $product2->setName('Shirt');
        $product2->setPrice('5000');
        $product2->setStockQuantity(10);
        $product2->setDescription('A nice shirt');

        $expectedProducts = [$product1, $product2];

        // Mock the findAll method
        $this->productRepository->expects($this->once())
            ->method('findAllProducts')
            ->willReturn($expectedProducts);

        $products = $this->productService->getAllProducts();

        // Assertions
        $this->assertCount(2, $products);
        $this->assertSame($expectedProducts, $products);
    }

    public function test_get_a_single_product_by_id()
    {
        $product = new Product();
        $product->setName('Dress');
        $product->setPrice('9000');
        $product->setStockQuantity(5);
        $product->setDescription('My new dress');

        $this->productRepository->expects($this->once())
            ->method('findOneBy')
            ->with($this->equalTo(['id' => 1]))
            ->willReturn($product);
       
        $singleProduct = $this->productService->getProductById(1);

        // Assertions
        $this->assertSame($product, $singleProduct);
        $this->assertEquals(1, $singleProduct->getId());
        $this->assertEquals('Dress', $singleProduct->getName());
        $this->assertEquals(9000, $singleProduct->getPrice());
        $this->assertEquals(5, $singleProduct->getStockQuantity());
        $this->assertEquals('My new dress', $singleProduct->getDescription());

    }


    public function test_can_create_new_product()
    {
        // create new product
        $product = new Product();
        $product->setName('Dress');
        $product->setPrice('9000');
        $product->setStockQuantity(5);
        $product->setDescription('My new dress');

        $this->entityManager->persist($product);

        // Do something
        $this->entityManager->flush();

        $productRepository = $this->entityManager->getRepository(Product::class);

        $productData = $productRepository->findOneBy(['name' => 'Dress']);

        // Make assertions
        $this->assertEquals('Dress', $productData->getName());
        $this->assertEquals('9000', $productData->getPrice());
        $this->assertEquals(5, $productData->getStockQuantity());
        $this->assertEquals('My new dress', $productData->getDescription());
    }

    public function test_can_update_an_existing_product()
    {
        //create new product
        $product = new Product();
        $product->setName('Dress');
        $product->setPrice('9000');
        $product->setStockQuantity(5);
        $product->setDescription('My new dress');

        //save the product
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $productRepository = $this->entityManager->getRepository(Product::class);

        $productData = $productRepository->findOneBy(['name' => 'Dress']);

        //create new date to update product
        $productData = [
            'name' => 'Updated Dress',
            'price' => 8000,
            'stock_quantity' => 10,
            'description' => 'Updated description'
        ];

        //call the store function
        $updatedProduct = $this->productService->store($productData, $product);

        // Make assertions
        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals('Updated Dress', $updatedProduct->getName());
        $this->assertEquals('8000', $updatedProduct->getPrice());
        $this->assertEquals(10, $updatedProduct->getStockQuantity());
        $this->assertEquals('Updated description', $updatedProduct->getDescription());
    }

    public function test_can_delete_product()
    {
        $productRepository = $this->createMock(ProductRepository::class);
        
        $productRepository = $this->entityManager->getRepository(Product::class);

        $productData = $productRepository->findOneBy(['name' => 'Dress']);

        $data = [
            'name' => 'Updated Dress',
            'price' => 8000,
            'stock_quantity' => 10,
            'description' => 'Updated description'
        ];

        $this->productService->deleteProduct($productData, $data);
    }
}
