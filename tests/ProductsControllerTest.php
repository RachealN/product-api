<?php

namespace App\Tests;

use App\Entity\Product;
use App\Service\ProductService;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductsControllerTest extends WebTestCase
{

    private ProductService $productService;
    private $productRepository;


    protected function setUp(): void
    {
        parent::setUp(); // Ensure to call parent method if you override setUp

        static::bootKernel(); 

        self::ensureKernelShutdown();
        static::createClient();

        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->productService = new ProductService($this->productRepository);
    }

    private function createAuthenticatedClient()
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->enableProfiler();

        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => 'testi@gmail.com',
                'password' => 'password'
            ])
        );

        $tokenContent = $client->getResponse()->getContent();

        $token = json_decode($tokenContent, true);

        if (!$token['token']) {
            throw new \Exception('Token not found in the response: ');
        }

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $token['token']));

        return $client;
    }

    public function test_user_login_successful(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->enableProfiler();

        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail('testi@gmail.com');

        $client->loginUser($testUser);

        $client->request('GET', '/api/products');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
    }

    public function test_fetching_all_products_successful()
    {
        $AuthenticatedUser = $this->createAuthenticatedClient();

        $AuthenticatedUser->request('GET', '/api/products');

        $this->assertEquals(Response::HTTP_OK, $AuthenticatedUser->getResponse()->getStatusCode());
        $this->assertJson($AuthenticatedUser->getResponse()->getContent());
        $this->assertResponseIsSuccessful();
    }

    public function test_create_product_successfully()
    {
        $authenticatedClient = $this->createAuthenticatedClient();
        $data = [
            'name' => 'Car',
            'price' => '5000',
            'stock_quantity' => 5,
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit'
        ];

        $authenticatedClient->request(
            'POST',
            '/api/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($data)
        );

        $productRepository = static::getContainer()->get(ProductRepository::class);
        $createdProduct = $productRepository->findOneBy(['name' => 'Car']);

        $this->assertNotNull($createdProduct);
        $this->assertSame('Car', $createdProduct->getName());
        $this->assertSame('5000', $createdProduct->getPrice());
        $this->assertSame(1, $createdProduct->getStockQuantity());
        $this->assertSame('Lorem ipsum dolor sit amet consectetur adipisicing elit', $createdProduct->getDescription());
    }


    public function test_can_update_existing_product(): void
    {
        $client = $this->createAuthenticatedClient();

        $productId = 1;
        $updatedData = [
            'name' => 'Updated Car',
            'price' => '45000',
            'stock_quantity' => 5,
            'description' => 'This is an updated product'
        ];

        $client->request(
            'PUT',
            '/api/products/' . $productId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updatedData)
        );


        $productRepository = static::getContainer()->get(ProductRepository::class);
        $updatedProduct = $productRepository->find($productId);

        $this->assertSame('Updated Car', $updatedProduct->getName());
        $this->assertSame('45000', $updatedProduct->getPrice());
        $this->assertSame(5, $updatedProduct->getStockQuantity());
        $this->assertSame('This is an updated product', $updatedProduct->getDescription());
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }


    public function test_can_delete_product(): void
    {
        $client = $this->createAuthenticatedClient();

        $productId = 1;

        $client->request('DELETE', '/api/products/' . $productId);


        $productRepository = static::getContainer()->get(ProductRepository::class);
        $deletedProduct = $productRepository->find($productId);


        $this->assertNull($deletedProduct);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
