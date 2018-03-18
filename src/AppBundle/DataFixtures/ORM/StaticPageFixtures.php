<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Image;
use AppBundle\Entity\StaticPage;
use Doctrine\Common\Persistence\ObjectManager;

class StaticPageFixtures extends FixtureHelper
{
    /** @var ObjectManager */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadDefaults();
        $this->loadOthers();
    }

    private function loadDefaults()
    {
        $pages = [
            'Mentions légales',
            'Conditions Générales d\'utilisation et de Ventes'
        ];

        foreach ($pages as $page) {
            $staticPage = new StaticPage();
            $staticPage->setTitle($page)
                ->setContent($this->faker->paragraph(100))
                ->setPublishedAt(new \DateTime())
                ->setIsDeletable(false);
            $this->manager->persist($staticPage);
        }

        $this->manager->flush();
    }

    private function loadOthers()
    {
        for ($sp = 1; $sp <= 3; $sp++) {
            $staticPage = new StaticPage();
            $staticPage->setTitle('Titre de la page statique n°' . $sp)
                ->setContent($this->faker->paragraph(100))
                ->setPublishedAt(new \DateTime())
                ->setImage($this->loadImage());

            $this->setReference('staticPage-' . $sp, $staticPage);
            $this->manager->persist($staticPage);
        }
        $this->manager->flush();
    }

    private function loadImage()
    {
        $uploadPath = $this->getUploadDir('static-page');
        $fixturesPath = $this->getImgFixturesDir('static-page');

        $file = $this->faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $this->manager->persist($image);

        $this->manager->flush();
        return $image;
    }
}
