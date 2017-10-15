<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ImageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        $uploadPath =  __DIR__ . '/../../../../uploads/product';
        $fixturesPath =  __DIR__ . '/../img/product';
        for ($i = 1; $i <= 2; $i++) {
            for ($j = 1; $j <= 5; $j++) {
                $image = new Image();
                $file = $faker->file($fixturesPath, $uploadPath, false);

                $image->setPath($file)
                    ->setProduct($this->getReference('product-' . $i . '-' .$j));

                $this->setReference('image-product-' . $i, $image);
                $manager->persist($image);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProductFixtures::class];
    }
}
