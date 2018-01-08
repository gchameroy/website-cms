<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\ProductSkill;
use Doctrine\Common\Persistence\ObjectManager;

class ProductSkillFixtures extends FixtureHelper
{
    public function load(ObjectManager $manager)
    {
        for ($p = 1; $p <= self::NB_PRODUCT; $p++) {
            for ($s = 1; $s <= self::NB_PRODUCT_SKILL; $s++) {
                $skill = (new ProductSkill())
                    ->setLabel($this->faker->word)
                    ->setValue($this->faker->randomNumber(4))
                    ->setProduct($this->getReference('product-' . $p));
                $manager->persist($skill);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProductFixtures::class];
    }
}
