<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Gallery;
use AppBundle\Entity\Image;
use Doctrine\Common\Persistence\ObjectManager;

class GalleryFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        for ($g = 1; $g <= self::NB_GALLERY; $g++) {
            $gallery = (new Gallery())
                ->setTitle($this->faker->text(15))
                ->setDescription($this->faker->text(150))
                ->setPublishedAt($date)
                ->setImage($this->loadImage($manager));

            $this->setReference('gallery-' . $g, $gallery);
            $manager->persist($gallery);
        }
        $manager->flush();
    }

    private function loadImage(objectManager $manager)
    {
        $uploadPath =  __DIR__ . '/../../../../uploads/gallery';
        $fixturesPath =  __DIR__ . '/../img/gallery';

        $file = $this->faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $manager->persist($image);

        $manager->flush();
        return $image;
    }
}
