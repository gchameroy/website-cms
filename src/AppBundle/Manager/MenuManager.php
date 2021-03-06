<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Menu;
use AppBundle\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MenuManager
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var MenuRepository */
    private $menuRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->menuRepository = $entityManager->getRepository(Menu::class);
    }

    public function getNextOrder(): int
    {
        /** @var MenuRepository $menuRepository */
        $menuRepository = $this->entityManager->getRepository(Menu::class);
        $menu = $menuRepository->getLast();

        if (!$menu) {
            return 1;
        }

        return $menu->getOrder() + 1;
    }

    public function get(int $id): Menu
    {
        /** @var $menu Menu */
        $menu = $this->menuRepository->find($id);
        $this->checkMenu($menu);

        return $menu;
    }

    public function getNew(): Menu
    {
        return new Menu();
    }

    public function getLast(): Menu
    {
        return $this->menuRepository->getLast();
    }

    public function getPrevious(Menu $menu): Menu
    {
        /** @var Menu $previousMenu */
        $previousMenu = $this->menuRepository->findOneBy([
            ['order' => $menu->getOrder() - 1]
        ]);
        $this->checkMenu($previousMenu);

        return $previousMenu;
    }

    public function getNext(Menu $menu): Menu
    {
        /** @var Menu $nextMenu */
        $nextMenu = $this->menuRepository->findOneBy([
            ['order' => $menu->getOrder() + 1]
        ]);
        $this->checkMenu($nextMenu);

        return $nextMenu;
    }

    public function getList(): array
    {
        return $this->menuRepository->findAll();
    }

    public function save(Menu $menu): Menu
    {
        $this->entityManager->persist($menu);
        $this->entityManager->flush();

        return $menu;
    }

    public function remove(? Menu $menu): void
    {
        if (!$menu) {
            return;
        }

        $this->entityManager->remove($menu);
        $this->entityManager->flush();
    }

    public function checkMenu(?Menu $menu): void
    {
        if(!$menu) {
            throw new NotFoundHttpException('Menu Not Found.');
        }
    }
}
