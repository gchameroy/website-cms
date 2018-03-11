<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use AppBundle\Entity\Menu;
use AppBundle\Entity\Product;
use AppBundle\Entity\StaticPage;
use AppBundle\Form\Type\Menu\MenuDeleteType;
use AppBundle\Form\Type\Menu\MenuType;
use AppBundle\Form\Type\Menu\MenuPublishType;
use AppBundle\Form\Type\Menu\MenuUnpublishType;
use AppBundle\Manager\MenuManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/menus")
 */
class MenuController Extends Controller
{
    /**
     * @Route("/", name="admin_menus")
     * @return Response
     */
    public function listAction(MenuManager $menuManager)
    {
        $menus = $menuManager->getList();

        return $this->render('admin/menu/list.html.twig', [
            'menus' => $menus
        ]);
    }

    /**
     * @Route("/add", name="admin_menus_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request, MenuManager $menuManager)
    {
        $em = $this->getDoctrine()->getManager();
        $menu = new Menu();

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setOrder($menuManager->getNextOrder());
            $menuManager->save($menu);

            return $this->redirectToRoute('admin_menus');
        }

        $categories = $em->getRepository(Category::class)
            ->findAll();
        $staticPages = $em->getRepository(StaticPage::class)
            ->findAll();
        $products = $em->getRepository(Product::class)
            ->findAll();

        return $this->render('admin/menu/add.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
            'staticPages' => $staticPages,
            'products' => $products,
        ]);
    }

    /**
     * @Route("/{menu}/edit", name="admin_menu_edit", requirements={"menu": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, int $menu, MenuManager $menuManager)
    {
        $menu = $menuManager->get($menu);

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menuManager->save($menu);

            return $this->redirectToRoute('admin_menus', [
                'menu' => $menu,
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)
            ->findAll();
        $staticPages = $em->getRepository(StaticPage::class)
            ->findAll();
        $products = $em->getRepository(Product::class)
            ->findAll();

        return $this->render('admin/menu/edit.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
            'categories' => $categories,
            'staticPages' => $staticPages,
            'products' => $products,
        ]);
    }

    /**
     * @Route("/{menu}/delete", name="admin_menu_delete", requirements={"menu": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function deleteAction(int $menu, Request $request, MenuManager $menuManager)
    {
        $menu = $menuManager->get($menu);

        $form = $this->createForm(MenuDeleteType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menuManager->remove($menu);

            return $this->redirectToRoute('admin_menus', [
                'menu' => $menu,
            ]);
        }

        return $this->render('admin/menu/delete.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu
        ]);
    }

    /**
     * @Route("/{menu}/publish", name="admin_menu_publish", requirements={"menu": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function publishAction(Request $request, int $menu, MenuManager $menuManager)
    {
        $menu = $menuManager->get($menu);
        $menu->setPublishedAt(new \DateTime());

        $form = $this->createForm(MenuPublishType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menuManager->save($menu);

            return $this->redirectToRoute('admin_menus');
        }

        return $this->render('admin/menu/publish.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/{menu}/unpublish", name="admin_menu_unpublish", requirements={"menu": "\d+"})
     * @Method({"GET", "POST"})
     */
    public function unpublishAction(Request $request, int $menu, MenuManager $menuManager)
    {
        $menu = $menuManager->get($menu);

        if (null === $menu->getPublishedAt()) {
            return $this->redirectToRoute('admin_menus');
        }

        $form = $this->createForm(MenuUnpublishType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setPublishedAt(null);
            $menuManager->save($menu);

            return $this->redirectToRoute('admin_menus');
        }

        return $this->render('admin/menu/unpublish.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/move", name="admin_menu_move")
     * @Method({"POST"})
     */
    public function upAction(Request $request, EntityManagerInterface $entityManager, MenuManager $menuManager)
    {
        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('menu-move', $token)) {
            $menu = $menuManager->get($request->request->get('menu'));

            $direction = $request->request->get('direction');
            if (!in_array($direction, ['up', 'down'])) {
                return $this->redirectToRoute('admin_menus');
            }

            if ($direction == 'up') {
                if ($menu->getOrder() == 1) {
                    return $this->redirectToRoute('admin_menus');
                }

                /** @var Menu $menu2 */
                $menu2 = $entityManager->getRepository(Menu::class)
                    ->findOneByOrder($menu->getOrder() - 1);
            } else {
                $last = $entityManager->getRepository(Menu::class)
                    ->findLast();
                if ($menu === $last) {
                    return $this->redirectToRoute('admin_menus');
                }

                /** @var Menu $menu2 */
                $menu2 = $entityManager->getRepository(Menu::class)
                    ->findOneByOrder($menu->getOrder() + 1);
            }

            $order = $menu->getOrder();
            $order2 = $menu2->getOrder();

            $menu->setOrder(null);
            $menu2->setOrder(null);
            $entityManager->persist($menu);
            $entityManager->persist($menu2);
            $entityManager->flush();


            $menu->setOrder($order2);
            $menu2->setOrder($order);
            $entityManager->persist($menu);
            $entityManager->persist($menu2);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_menus');
    }
}
