<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\DeliveryZone;
use Doctrine\Common\Persistence\ObjectManager;

class DeliveryZoneFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $deliveryZones = [
            ['name' => 'France', 'price' => '15']
        ];

        $z = 1;
        foreach ($deliveryZones as $dz) {
            $deliveryZone = (new DeliveryZone())
                ->setName($dz['name'])
                ->setPrice($dz['price']);

            $this->setReference('delivery-zone-' . $z++, $deliveryZone);
            $manager->persist($deliveryZone);
        }

        $manager->flush();
    }
}
