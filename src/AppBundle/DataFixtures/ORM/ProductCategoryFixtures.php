<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Category;
use Doctrine\Common\Persistence\ObjectManager;

class ProductCategoryFixtures extends FixtureHelper
{
    private $categories = [
        'Coutellerie',
        'Vannerie'
    ];

    public function load(ObjectManager $manager)
    {
        for ($c = 1; $c <= self::NB_PRODUCT_CATEGORY; $c++) {
            $category = new Category();
            $category->setLabel($this->categories[$c - 1])
                ->setDescription($this->faker->sentence())
                ->setPosition($c);
            $category->setPublishedAt(new \DateTime());
            $this->setReference('product-category-' . $c, $category);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
