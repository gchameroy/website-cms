<?php

namespace AppBundle\Service;

use AppBundle\Entity\Menu;
use AppBundle\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CartManager
 * @package AppBundle\Service
 */
class MenuManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return int
     */
    public function getNextOrder()
    {
        /** @var MenuRepository $menuRepository */
        $menuRepository = $this->em->getRepository(Menu::class);
        $menu = $menuRepository->findLast();

        if (!$menu) {
            return 1;
        }

        return $menu->getOrder() + 1;
    }
}
