<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Image;
use Doctrine\Common\Persistence\ObjectManager;

class ImageFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $uploadPath =  __DIR__ . '/../../../../uploads/product';
        $fixturesPath =  __DIR__ . '/../img/product';
        for ($p = 1; $p <= self::NB_PRODUCT; $p++) {
            $image = new Image();
            $file = $this->faker->file($fixturesPath, $uploadPath, false);

            $image->setPath($file)
                ->setProduct($this->getReference('product-' . $p));

            $this->setReference('image-product-' . $p, $image);
            $manager->persist($image);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProductFixtures::class];
    }
}
