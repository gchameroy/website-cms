<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Image;
use AppBundle\Entity\Product;
use Doctrine\Common\Persistence\ObjectManager;

class ProductVariantFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        for ($p = 1; $p <= self::NB_PRODUCT; $p++) {
            for ($v = 1; $v <= self::NB_PRODUCT_VARIANT; $v++) {
                $product = (new Product())
                    ->setParent($this->getReference('product-' . $p))
                    ->setVariantName(sprintf('Pot de %sg',  $v * 100))
                    ->setReference('P' . $date->format('ym') . '-' . str_pad($p, 3, '0', STR_PAD_LEFT) . '-V' . $v)
                    ->setPublishedAt($date)
                    ->setImage($this->loadImage($manager));

                $this->setReference('product-' . $p . '-variant-' . $v, $product);
                $manager->persist($product);
            }
        }
        $manager->flush();
    }

    private function loadImage(objectManager $manager)
    {
        $uploadPath = __DIR__ . '/../../../../uploads/product';
        $fixturesPath = __DIR__ . '/../img/product';

        $file = $this->faker->file($fixturesPath, $uploadPath, false);
        $image = new Image();
        $image->setPath($file);
        $manager->persist($image);

        $manager->flush();
        return $image;
    }

    public function getDependencies()
    {
        return [ProductFixtures::class];
    }
}
