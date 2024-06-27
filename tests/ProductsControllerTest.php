<?php

namespace App\Tests;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;



class ProductsControllerTest extends WebTestCase
{
    
    public function test_can_get_all_products()
    {
        $client = static::createClient();

        $client->request('GET', '/products');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);

    }

    public function test_create_product()
    {
        $client = static::createClient();

        $data = [
            'name' => 'New Product',
            'price' => 20.99,
            'stock_quantity' => 100,
            'description' => 'A new product description.'
        ];

        $client->request('POST', '/product/create', [], [], [], json_encode($data));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Product created successfully', $responseData['message']);
        $this->assertArrayHasKey('product', $responseData);
        $this->assertEquals('New Product', $responseData['product']['name']);
    }

    public function testUpdateProduct()
    {
        $client = static::createClient();
        
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Create a product first
        $product = new Product();
        $product->setName('Initial Product');
        $product->setPrice('500');
        $product->setStockQuantity(50);
        $product->setDescription('Initial product description.');
        $entityManager->persist($product);
        $entityManager->flush();

        $data = [
            'name' => 'Updated Product',
            'price' => "800",
            'stock_quantity' => 75,
            'description' => 'Updated product description.'
        ];

        $client->request('PUT', '/product/' . $product->getId(), [], [], [], json_encode($data));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Product updated successfully', $responseData['message']);
        $this->assertArrayHasKey('product', $responseData);
        $this->assertEquals('Updated Product', $responseData['product']['name']);
    }

    public function test_delete_product()
    {
        $client = static::createClient();

        $data = [
            'name' => 'New Product',
            'price' => 20.99,
            'stock_quantity' => 100,
            'description' => 'A new product description.'
        ];
        $client->request('POST', '/product/create', [], [], [], json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Product created successfully', $responseData['message']);
        $this->assertArrayHasKey('product', $responseData);
        $productId = $responseData['product']['id'];

        $client->request('DELETE', '/product/' . $productId . '/delete');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Product deleted successfully', $responseData['message']);
    }
}