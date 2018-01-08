<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\OrderProduct;
use AppBundle\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;

class OrderProductFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $this->loadForUser($manager);
        $this->loadForPro($manager);
    }

    private function loadForUser(ObjectManager $manager)
    {
        for ($u = 1; $u <= self::NB_USER; $u++) {
            for ($o = 1; $o <= self::NB_ORDER_USER; $o++) {
                for ($op = 1; $op <= self::NB_USER_ORDER_PRODUCT; $op++) {
                    /** @var Product $product */
                    $product = $this->getReference('product-' . $this->faker->numberBetween(1, self::NB_PRODUCT));
                    $quantity = $this->faker->numberBetween(self::MIN_QTY_USER_PRODUCT, self::MAX_QTY_USER_PRODUCT);
                    $orderProduct = (new OrderProduct())
                        ->setProduct($product)
                        ->setPrice(0)
                        ->setQuantity($quantity)
                        ->setOrder($this->getReference('order-user-' . $u . '-' . $o));
                    $this->setReference('order-product-' . $u . '-' . $o . '-' . $op, $orderProduct);
                    $manager->persist($orderProduct);
                }
            }
        }

        $manager->flush();
    }

    private function loadForPro(ObjectManager $manager)
    {
        for ($u = 1; $u <= self::NB_PRO; $u++) {
            for ($o = 1; $o <= self::NB_ORDER_PRO; $o++) {
                for ($op = 1; $op <= self::NB_PRO_ORDER_PRODUCT; $op++) {
                    /** @var Product $product */
                    $product = $this->getReference('product-' . $this->faker->numberBetween(1, self::NB_PRODUCT));
                    $quantity = $this->faker->numberBetween(self::MIN_QTY_PRO_PRODUCT, self::MAX_QTY_PRO_PRODUCT);
                    $orderProduct = (new OrderProduct())
                        ->setProduct($product)
                        ->setPrice(0)
                        ->setQuantity($quantity)
                        ->setOrder($this->getReference('order-pro-' . $u . '-' . $o));
                    $this->setReference('order-product-' . $u . '-' . $o . '-' . $op, $orderProduct);
                    $manager->persist($orderProduct);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [OrderFixtures::class, ProductFixtures::class];
    }
}
