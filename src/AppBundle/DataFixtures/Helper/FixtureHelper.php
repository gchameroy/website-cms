<?php

namespace AppBundle\DataFixtures\Helper;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory as Faker;

abstract class FixtureHelper extends Fixture
{
    const NB_USER = 15;
    const NB_PRO = 5;
    const NB_ORDER_USER = 1;
    const NB_ORDER_PRO = 2;
    const NB_NEWSLETTER = 5;
    const NB_USER_ORDER_PRODUCT = 3;
    const NB_PRO_ORDER_PRODUCT = 5;
    const MIN_QTY_USER_PRODUCT = 1;
    const MAX_QTY_USER_PRODUCT = 3;
    const MIN_QTY_PRO_PRODUCT = 15;
    const MAX_QTY_PRO_PRODUCT = 45;
    const NB_PRODUCT = 25;
    const NB_PRODUCT_VARIANT = 2;
    const NB_PRODUCT_CATEGORY = 2;
    const NB_PRODUCT_SKILL = 2;
    const NB_DELIVERY_ZONE = 1;

    /**
     * @var \Faker\Generator
     */
    public $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function getUploadDir($folder = ''): string
    {
        $folder = '' !== $folder ? '/' . $folder : '';

        return __DIR__ . '/../../../../uploads' . $folder;
    }

    public function getImgFixturesDir($folder = ''): string
    {
        $folder = '' !== $folder ? '/' . $folder : '';

        return __DIR__ . '/../img' . $folder;
    }
}
