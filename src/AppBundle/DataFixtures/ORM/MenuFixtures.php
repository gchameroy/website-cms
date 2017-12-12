<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DataFixtures\Helper\FixtureHelper;
use AppBundle\Entity\Category;
use AppBundle\Entity\Menu;
use Doctrine\Common\Persistence\ObjectManager;

class MenuFixtures extends FixtureHelper
{
    /** @var ObjectManager $manager */
    private $manager;

    /** @var integer $order */
    private $order = 1;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadDefaults();
        $this->loadForCategories();

        $this->manager->flush();
    }

    private function loadDefaults()
    {
        $menu = (new Menu())
            ->setPageName('Accueil')
            ->setRouteName('front_home')
            ->setOrder($this->order++)
            ->setPublishedAt(new \DateTime())
            ->setIsDeletable(false);
        $this->setReference('menu-home', $menu);
        $this->manager->persist($menu);
    }

    private function loadForCategories()
    {
        for ($c = 1; $c < self::NB_PRODUCT_CATEGORY; ++$c) {
            /** @var Category $category */
            $category = $this->getReference('product-category-' . $c);
            $menu = (new Menu())
                ->setPageName($category->getLabel())
                ->setRouteName('front_category')
                ->setRouteSlug($category->getSlug())
                ->setOrder($this->order++)
                ->setPublishedAt(new \DateTime());
            $this->setReference('menu-category-' . $c, $menu);
            $this->manager->persist($menu);
        }
    }

    public function getDependencies()
    {
        return [ProductCategoryFixtures::class];
    }
}
