<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\ProductPrice;
use Doctrine\Common\Persistence\ObjectManager;

class ProductPriceFixtures extends FixtureHelper
{
    /** @var ObjectManager */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadForProducts();
        $this->loadForProductsVariants();
    }

    private function loadForProducts()
    {
        for ($p = 1; $p <= self::NB_PRODUCT; $p++) {
            $pprice = $this->faker->numberBetween(10, 15);
            $price = (new ProductPrice())
                ->setPrice($pprice)
                ->setOffer($this->getReference('user-offer-none'))
                ->setProduct($this->getReference('product-' . $p));
            $this->manager->persist($price);

            $pprice = $pprice - $this->faker->numberBetween(1, 5);
            $price = (new ProductPrice())
                ->setPrice($pprice)
                ->setOffer($this->getReference('user-offer-pro'))
                ->setProduct($this->getReference('product-' . $p));
            $this->manager->persist($price);
        }

        $this->manager->flush();
    }

    private function loadForProductsVariants()
    {
        for ($p = 1; $p <= self::NB_PRODUCT; $p++) {
            for ($v = 1; $v <= self::NB_PRODUCT_VARIANT; $v++) {
                $pprice = $this->faker->numberBetween(10, 15);
                $price = (new ProductPrice())
                    ->setPrice($pprice)
                    ->setOffer($this->getReference('user-offer-none'))
                    ->setProduct($this->getReference('product-' . $p . '-variant-' . $v));
                $this->manager->persist($price);

                $pprice = $pprice - $this->faker->numberBetween(1, 5);
                $price = (new ProductPrice())
                    ->setPrice($pprice)
                    ->setOffer($this->getReference('user-offer-pro'))
                    ->setProduct($this->getReference('product-' . $p . '-variant-' . $v));
                $this->manager->persist($price);
            }
        }

        $this->manager->flush();
    }

    public function getDependencies()
    {
        return [ProductFixtures::class, ProductVariantFixtures::class, UserOfferFixtures::class];
    }
}
