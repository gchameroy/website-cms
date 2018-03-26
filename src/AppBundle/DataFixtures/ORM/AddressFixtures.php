<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Address;
use Doctrine\Common\Persistence\ObjectManager;

class AddressFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $this->loafForAdmin($manager);
        $this->loafForUser($manager);
        $this->loadForPro($manager);
        $this->loafForPointOfSale($manager);
    }

    private function loafForAdmin(ObjectManager $manager)
    {
        $address = (new Address())
            ->setAddress($this->faker->address)
            ->setCity($this->faker->city)
            ->setCountry($this->faker->country)
            ->setZipCode($this->faker->numberBetween(52000, 52999));
        $this->setReference('address-admin', $address);
        $manager->persist($address);
        $manager->flush();
    }

    private function loafForUser(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_USER; $i++) {
            $address = (new Address())
                ->setAddress($this->faker->address)
                ->setCity($this->faker->city)
                ->setCountry($this->faker->country)
                ->setZipCode($this->faker->numberBetween(52000, 52999));
            $this->setReference('address-user-' . $i, $address);
            $manager->persist($address);
        }
        $manager->flush();
    }

    private function loadForPro(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_PRO; $i++) {
            $address = (new Address())
                ->setAddress($this->faker->address)
                ->setCity($this->faker->city)
                ->setCountry($this->faker->country)
                ->setZipCode($this->faker->numberBetween(52000, 52999));
            $this->setReference('address-pro-' . $i, $address);
            $manager->persist($address);
        }
        $manager->flush();
    }

    private function loafForPointOfSale(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::NB_POINT_OF_SALE; $i++) {
            $address = (new Address())
                ->setCompany($this->faker->company)
                ->setAddress($this->faker->address)
                ->setCity($this->faker->city)
                ->setCountry($this->faker->country)
                ->setZipCode($this->faker->numberBetween(52000, 52999))
                ->setLat($this->faker->latitude)
                ->setLng($this->faker->longitude);
            $this->setReference('address-point-of-sale-' . $i, $address);
            $manager->persist($address);
        }
        $manager->flush();
    }
}
