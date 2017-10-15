<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class OrderFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 2; $i++) {
            for ($j = 1; $j <= 10; $j++) {
                $order = (new Order())
                    ->setUser($this->getReference('user-admin'))
                    ->setDeliveryAddress($this->getReference('address-' . $i . '-' . $j))
                    ->setCreatedAt(new \DateTime());
                $this->setReference('order-' . $i . '-' . $j, $order);
                $manager->persist($order);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class, AddressFixtures::class];
    }
}
