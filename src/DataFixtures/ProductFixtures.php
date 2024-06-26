<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Car');
        $product->setPrice('5000');
        $product->setStockQuantity(1);
        $product->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit');
        $manager->persist($product);

        $product2 = new Product();
        $product2->setName('phone');
        $product2->setPrice('20000');
        $product2->setStockQuantity(6);
        $product2->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing elit');
        $manager->persist($product2);

        $manager->flush();

    }
}
