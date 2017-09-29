<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Newsletter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class NewsletterFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 3; $i++) {
            $newsletter = new Newsletter();
            $newsletter->setTitle($faker->sentence(5))
                ->setContent($faker->paragraph)
                ->setPublishedAt(new \DateTime());

            $this->setReference('newsletter-' . $i, $newsletter);
            $manager->persist($newsletter);
        }
        $manager->flush();
    }
}
