<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        for ($p = 1; $p <= self::NB_PRODUCT; $p++) {
            $product = new Product();
            $product->setLabel('Product ' . $p)
                ->setDescription($this->faker->paragraph(3))
                ->setPrice($this->faker->numberBetween(10, 25))
                ->setPublishedAt($date)
                ->setCategory($this->getReference('product-category-' . $this->faker->numberBetween(1, self::NB_PRODUCT_CATEGORY)));

            $this->setReference('product-' . $p, $product);
            $manager->persist($product);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProductCategoryFixtures::class];
    }
}
