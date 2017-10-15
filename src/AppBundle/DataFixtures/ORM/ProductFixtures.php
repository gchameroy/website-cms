<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 2; $i++) {
            $date = new \DateTime();
            for ($j = 1; $j <= 15; $j++) {
                $product = new Product();
                $product->setLabel('Product 1')
                    ->setDescription($faker->paragraph(3))
                    ->setPrice($faker->numberBetween(10, 25))
                    ->setPublishedAt($date)
                    ->setCategory($this->getReference('product-category-' . $i));

                $this->setReference('product-' . $i . '-' . $j, $product);
                $manager->persist($product);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProductCategoryFixtures::class];
    }
}
