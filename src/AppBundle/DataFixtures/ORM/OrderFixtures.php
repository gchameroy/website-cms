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
        for ($u = 1; $u <= self::NB_USER; $u++) {
            for ($o = 1; $o <= self::NB_ORDER_USER; $o++) {
                $order = (new Order())
                    ->setUser($this->getReference('user-' . $u))
                    ->setDeliveryAddress($this->getReference('address-user-'. $u))
                    ->setBillingAddress($this->getReference('address-user-' . $u))
                    ->setCreatedAt(new \DateTime());
                $this->setReference('order-user-' . $u . '-' . $o, $order);
                $manager->persist($order);
            }
        }
        $manager->flush();
    }

    private function loadForPro(ObjectManager $manager)
    {
        for ($u = 1; $u <= self::NB_PRO; $u++) {
            for ($o = 1; $o <= self::NB_ORDER_PRO; $o++) {
                $order = (new Order())
                    ->setUser($this->getReference('user-pro-' . $u))
                    ->setDeliveryAddress($this->getReference('address-pro-'. $u))
                    ->setBillingAddress($this->getReference('address-pro-' . $u))
                    ->setCreatedAt(new \DateTime());
                $this->setReference('order-pro-' . $u . '-' . $o, $order);
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
