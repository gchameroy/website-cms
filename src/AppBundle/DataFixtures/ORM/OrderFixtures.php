<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Order;
use Doctrine\Common\Persistence\ObjectManager;

class OrderFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $this->loadForUser($manager);
        $this->loadForPro($manager);
    }

    private function loadForUser(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_USER; $i++) {
            for ($j = 1; $j <= self::NB_ORDER_USER; $j++) {
                $order = (new Order())
                    ->setUser($this->getReference('user-' . $i))
                    ->setDeliveryAddress($this->getReference('address-user-'. $i))
                    ->setBillingAddress($this->getReference('address-user-' . $i))
                    ->setCreatedAt(new \DateTime());
                $this->setReference('order-user-' . $i . '-' . $j, $order);
                $manager->persist($order);
            }
        }
        $manager->flush();
    }

    private function loadForPro(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_PRO; $i++) {
            for ($j = 1; $j <= self::NB_ORDER_PRO; $j++) {
                $order = (new Order())
                    ->setUser($this->getReference('user-pro-' . $i))
                    ->setDeliveryAddress($this->getReference('address-pro-'. $i))
                    ->setBillingAddress($this->getReference('address-pro-' . $i))
                    ->setCreatedAt(new \DateTime());
                $this->setReference('order-pro-' . $i . '-' . $j, $order);
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
