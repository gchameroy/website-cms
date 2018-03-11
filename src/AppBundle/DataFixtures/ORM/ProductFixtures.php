<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Image;
use AppBundle\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        for ($p = 1; $p <= self::NB_PRODUCT; $p++) {
            $product = new Product();
            $product->setLabel('Product ' . $p)
                ->setDescription($this->faker->paragraph(3))
                ->setVariantName('Pot de 50g')
                ->setMore1($this->faker->paragraph(10))
                ->setMore2($this->faker->paragraph(10))
                ->setMore3($this->faker->paragraph(10))
                ->setReference('P' . $date->format('ym') . '-' . str_pad($p, 3, '0', STR_PAD_LEFT))
                ->setPublishedAt($date)
                ->addImage($this->loadImage($manager))
                ->setCategory($this->getReference('product-category-' . $this->faker->numberBetween(1, self::NB_PRODUCT_CATEGORY)));

            $this->setReference('product-' . $p, $product);
            $manager->persist($product);
        }
        $manager->flush();
    }

    private function loadImage(objectManager $manager)
    {
        $uploadPath =  __DIR__ . '/../../../../uploads/product';
        $fixturesPath =  __DIR__ . '/../img/product';

        $file = $this->faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $manager->persist($image);

        $manager->flush();
        return $image;
    }

    public function getDependencies()
    {
        return [ProductCategoryFixtures::class];
    }
}
