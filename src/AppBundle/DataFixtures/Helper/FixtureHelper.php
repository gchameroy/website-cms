<?php

namespace AppBundle\DataFixtures\Helper;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory as Faker;

abstract class FixtureHelper extends Fixture
{
    const NB_USER = 15;
    const NB_PRO = 5;
    const NB_ORDER_USER = 3;
    const NB_ORDER_PRO = 7;
    const NB_NEWSLETTER = 5;
    const NB_USER_ORDER_PRODUCT = 3;
    const NB_PRO_ORDER_PRODUCT = 5;
    const MIN_QTY_USER_PRODUCT = 1;
    const MAX_QTY_USER_PRODUCT = 3;
    const MIN_QTY_PRO_PRODUCT = 15;
    const MAX_QTY_PRO_PRODUCT = 45;
    const NB_PRODUCT = 25;
    const NB_PRODUCT_CATEGORY = 3;

    /**
     * @var \Faker\Generator
     */
    public $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }
}
