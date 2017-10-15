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
        $categories = ['Vannerie', 'Coutellerie'];

        $i = 1;
        foreach ($categories as $label) {
            $category = new Category();
            $category->setLabel($label)
                ->setDescription('');
            $category->setPublishedAt(new \DateTime());
            $this->setReference('product-category-' . $i, $category);
            $manager->persist($category);
            ++$i;
        }

        $manager->flush();
    }
}
