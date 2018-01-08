<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\UserOffer;
use Doctrine\Common\Persistence\ObjectManager;

class UserOfferFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $offer = new UserOffer();
        $offer->setLabel('Sans offre');
        $this->setReference('user-offer-none', $offer);
        $manager->persist($offer);

        $offer = new UserOffer();
        $offer->setLabel('Pro');
        $this->setReference('user-offer-pro', $offer);
        $manager->persist($offer);

        $manager->flush();
    }
}
