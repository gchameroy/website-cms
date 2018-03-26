<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\PointOfSale;
use Doctrine\Common\Persistence\ObjectManager;

class PointOfSaleFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_POINT_OF_SALE; $i++) {
            $pointOfSale = (new PointOfSale())
                ->setAddress($this->getReference('address-point-of-sale-' . $i))
                ->setPhone($this->faker->phoneNumber)
                ->setWebsite($this->faker->url);

            $this->setReference('point-of-sale-' . $i, $pointOfSale);
            $manager->persist($pointOfSale);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [AddressFixtures::class];
    }
}
