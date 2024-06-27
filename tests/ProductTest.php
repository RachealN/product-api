<?php

namespace App\Tests;

use App\Kernel;
use App\Entity\Product;
use App\Service\ProductService;
use App\Repository\ProductRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    private $entityManager;

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        DatabasePrimer::prime($kernel);

        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;

        restore_exception_handler();
        restore_error_handler();
    }

    #[Test]
    public function test_can_get_all_products()
    {
        $product = new Product();
        $product->setName('Car');
        $product->setPrice('5000');
        $product->setStockQuantity(1);
        $product->setDescription('testing testing');

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        //product repository
        $productRepository = $this->entityManager->getRepository(Product::class);

        $stockProduct = $productRepository->findOneBy(['name' => 'Car']);
        $this->assertEquals('Car', $stockProduct->getName());
        $this->assertEquals('5000', $stockProduct->getPrice());
        $this->assertEquals(1 , $stockProduct->getStockQuantity());
        $this->assertEquals('testing testing', $stockProduct->getDescription());
    } 

    #[Test]
    public function test_can_create_new_product()
    {
        $newProduct = $this->createMock(ProductRepository::class);
        
        $data = [
            'name' => 'Test Product',
            'price' => "500",
            'stock_quantity' => 8,
            'description' => 'Hello test'
        ];

        $product = new Product();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setStockQuantity($data['stock_quantity']);
        $product->setDescription($data['description']);

        $newProduct->expects($this->once())
                       ->method('save')
                       ->with($this->equalTo($product));

        $service = new ProductService($newProduct);
        $createdProduct = $service->createProduct($data);

        $this->assertInstanceOf(Product::class, $createdProduct);
        $this->assertEquals($data['name'], $createdProduct->getName());
        $this->assertEquals($data['price'], $createdProduct->getPrice());
        $this->assertEquals($data['stock_quantity'], $createdProduct->getStockQuantity());
        $this->assertEquals($data['description'], $createdProduct->getDescription());
    }
}