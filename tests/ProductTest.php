<?php

namespace App\Tests;

use App\Kernel;
use App\Entity\Product;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    private $entityManager;

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
    }

    /**
     * @test
     */
    public function test_can_get_all_products()
    {
        $product = new Product();
        $product->setName('Car');
        $product->setPrice('5000');
        $product->setStockQuantity(1);
        $product->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit');

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        //product repository
        $productRepository = $this->entityManager->getRepository(Product::class);

        $stockProduct = $productRepository->findOneBy(['name' => 'Car']);
        $this->assertEquals('Car', $stockProduct->getName());
        $this->assertEquals('5000', $stockProduct->getPrice());
    } 
}