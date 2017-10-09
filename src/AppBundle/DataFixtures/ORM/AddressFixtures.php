<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Address;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AddressFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 10; $j++) {
                $address = (new Address())
                    ->setAddress($faker->sentence(15))
                    ->setCity($faker->sentence(10))
                    ->setCountry($faker->sentence(10))
                    ->setZipCode($faker->randomNumber(5));
                $this->setReference('address-' . $i . '-' . $j, $address);
                $manager->persist($address);
            }
        }
        $manager->flush();
    }
}
