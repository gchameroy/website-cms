<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\UserOffer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserOfferFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $offer = new UserOffer();
        $offer->setLabel('Pro');
        $this->setReference('user-offer-pro', $offer);
        $manager->persist($offer);

        $manager->flush();
    }
}
