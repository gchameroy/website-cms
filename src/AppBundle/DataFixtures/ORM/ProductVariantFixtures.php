<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
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
                    ->setReference('P' . $date->format('ym') . '-' . str_pad($p, 3, '0', STR_PAD_LEFT) . '-V' . $v)
                    ->setPublishedAt($date);

                $this->setReference('product-' . $p . '-variant-' . $v, $product);
                $manager->persist($product);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProductFixtures::class];
    }
}
