<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\OrderProduct;
use AppBundle\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class OrderProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 10; $j++) {
                /** @var Product $product */
                $product = $this->getReference('product-' . $i . '-' .$j);
                $quantity = $faker->numberBetween(1, 5);
                $orderProduct = (new OrderProduct())
                    ->setProduct($product)
                    ->setPrice($product->getPrice() * $quantity)
                    ->setQuantity($quantity)
                    ->setOrder($this->getReference('order-' . $i . '-' .$j));
                $this->setReference('order-product-' . $i . '-' . $j, $orderProduct);
                $manager->persist($orderProduct);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [OrderFixtures::class, ProductFixtures::class];
    }
}
