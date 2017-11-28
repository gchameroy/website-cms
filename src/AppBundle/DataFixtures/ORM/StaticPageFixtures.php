<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Image;
use AppBundle\Entity\Newsletter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class StaticPageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 3; $i++) {
            $staticPage = new Newsletter();
            $staticPage->setTitle('Titre de la page statique n°' . $i)
                ->setContent($faker->paragraph(100))
                ->setPublishedAt(new \DateTime())
                ->setImage($this->loadImage($manager));

            $this->setReference('staticPage-' . $i, $staticPage);
            $manager->persist($staticPage);
        }
        $manager->flush();
    }

    private function loadImage(objectManager $manager)
    {
        $faker = Faker::create();
        $uploadPath = __DIR__ . '/../../../../uploads/static-page';
        $fixturesPath = __DIR__ . '/../img/static-page';

        $file = $faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $manager->persist($image);

        $manager->flush();
        return $image;
    }
}
