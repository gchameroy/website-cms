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
        $this->loadForProducts($manager);
        $this->loadForNewsletter($manager);
    }

    private function loadForProducts(objectManager $manager)
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

    private function loadForNewsletter(objectManager $manager)
    {
        $faker = Faker::create();
        $uploadPath =  __DIR__ . '/../../../../uploads/newsletter';
        $fixturesPath =  __DIR__ . '/../img/newsletter';
        for ($i = 1; $i <= 3; $i++) {
            $image = new Image();
            $file = $faker->file($fixturesPath, $uploadPath, false);

            $image->setPath($file)
                ->setNewsletter($this->getReference('newsletter-' . $i));

            $this->setReference('image-newsletter-' . $i, $image);
            $manager->persist($image);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class,
            NewsletterFixtures::class
        ];
    }
}
