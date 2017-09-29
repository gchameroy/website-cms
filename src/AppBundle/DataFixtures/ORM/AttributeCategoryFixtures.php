<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CategoryAttribute;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AttributeCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 3; $i++) {
            $category = new CategoryAttribute();
            $category->setLabel($faker->word);

            $this->setReference('attribute-category-' . $i, $category);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
