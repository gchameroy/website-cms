<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory as Faker;

class ProductCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category();
            $category->setLabel($faker->sentence(5))
                ->setDescription($faker->paragraph(1));
            $category->setPublishedAt(new \DateTime());

            $this->setReference('product-category-' . $i, $category);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
