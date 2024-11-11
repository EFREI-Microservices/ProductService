<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('product ' . $i);
            $product->setDescription("product test");
            $product->setPrice(mt_rand(10, 100));
            $product->setAvailable(mt_rand(0, 1));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
