<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Image;
use AppBundle\Entity\StaticPage;
use Doctrine\Common\Persistence\ObjectManager;

class StaticPageFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        for ($sp = 1; $sp <= 3; $sp++) {
            $staticPage = new StaticPage();
            $staticPage->setTitle('Titre de la page statique n°' . $sp)
                ->setContent($this->faker->paragraph(100))
                ->setPublishedAt(new \DateTime())
                ->setImage($this->loadImage($manager));

            $this->setReference('staticPage-' . $sp, $staticPage);
            $manager->persist($staticPage);
        }
        $manager->flush();
    }

    private function loadImage(objectManager $manager)
    {
        $uploadPath = __DIR__ . '/../../../../uploads/static-page';
        $fixturesPath = __DIR__ . '/../img/static-page';

        $file = $this->faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $manager->persist($image);

        $manager->flush();
        return $image;
    }
}
