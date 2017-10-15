<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Image;
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
            $newsletter->setTitle('Titre de l\'actualité')
                ->setContent($faker->paragraph(100))
                ->setPublishedAt(new \DateTime())
                ->setImage($this->loadImage($manager));

            $this->setReference('newsletter-' . $i, $newsletter);
            $manager->persist($newsletter);
        }
        $manager->flush();
    }

    private function loadImage(objectManager $manager)
    {
        $faker = Faker::create();
        $uploadPath =  __DIR__ . '/../../../../uploads/newsletter';
        $fixturesPath =  __DIR__ . '/../img/newsletter';

        $file = $faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $manager->persist($image);

        $manager->flush();
        return $image;
    }
}
