<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Attribute;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AttributeFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 3; $i++) {
            for ($j = 1; $j <= 3; $j++) {
                $attribute = new Attribute();
                $attribute->setLabel($faker->word)
                    ->setCategoryAttribute($this->getReference('attribute-category-' . $i));

                $this->setReference('attribute-' . $i . '-' . $j, $attribute);
                $manager->persist($attribute);
            }
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [AttributeCategoryFixtures::class];
    }
}
