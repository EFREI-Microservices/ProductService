<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Random\RandomException;

final class AppFixtures extends Fixture
{
    /**
     * @throws RandomException
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $product = (new Product())
                ->setName("Product $i")
                ->setDescription("Description product test $i")
                ->setPrice(random_int(10, 100))
                ->setAvailable(random_int(0, 1));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
