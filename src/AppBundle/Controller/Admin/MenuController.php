<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Form\Type\Menu\MenuDeleteType;
use AppBundle\Form\Type\Menu\MenuType;
use AppBundle\Form\Type\Menu\MenuPublishType;
use AppBundle\Form\Type\Menu\MenuUnpublishType;
use AppBundle\Manager\CategoryManager;
use AppBundle\Manager\MenuManager;
use AppBundle\Manager\ProductManager;
use AppBundle\Manager\StaticPageManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/menus")
 */
class MenuController Extends Controller
{
    /** @var MenuManager */
    private $menuManager;

    /** @var CategoryManager */
    private $categoryManager;

    /** @var StaticPageManager */
    private $staticPageManager;

    /** @var ProductManager */
    private $productManager;

    public function __construct(
        MenuManager $menuManager,
        CategoryManager $categoryManager,
        StaticPageManager $staticPageManager,
        ProductManager $productManager
    )
    {
        $this->menuManager = $menuManager;
        $this->categoryManager = $categoryManager;
        $this->staticPageManager = $staticPageManager;
        $this->productManager = $productManager;
    }

    /**
     * @Route("/", name="admin_menus")
     * @return Response
     */
    public function listAction(): Response
    {
        $menus = $this->menuManager->getList();

        return $this->render('admin/menu/list.html.twig', [
            'menus' => $menus
        ]);
    }

    /**
     * @Route("/add", name="admin_menus_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request): Response
    {
        $menu = $this->menuManager->getNew();

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setOrder($this->menuManager->getNextOrder());
            $this->menuManager->save($menu);

            return $this->redirectToRoute('admin_menus');
        }

        $categories = $this->categoryManager->getList();
        $staticPages = $this->staticPageManager->getList();
        $products = $this->productManager->getList();

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
     * @param int $menu
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editAction(int $menu, Request $request): Response
    {
        $menu = $this->menuManager->get($menu);

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->menuManager->save($menu);

            return $this->redirectToRoute('admin_menus');
        }

        $categories = $this->categoryManager->getList();
        $staticPages = $this->staticPageManager->getList();
        $products = $this->productManager->getList();

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
     * @param int $menu
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction(int $menu, Request $request): Response
    {
        $menu = $this->menuManager->get($menu);

        $form = $this->createForm(MenuDeleteType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->menuManager->remove($menu);

            return $this->redirectToRoute('admin_menus');
        }

        return $this->render('admin/menu/delete.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/{menu}/publish", name="admin_menu_publish", requirements={"menu": "\d+"})
     * @Method({"GET", "POST"})
     * @param int $menu
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function publishAction(int $menu, Request $request): Response
    {
        $menu = $this->menuManager->get($menu);
        $menu->setPublishedAt(new \DateTime());

        $form = $this->createForm(MenuPublishType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->menuManager->save($menu);

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
     * @param int $menu
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function unpublishAction(int $menu, Request $request): Response
    {
        $menu = $this->menuManager->get($menu);

        if (null === $menu->getPublishedAt()) {
            return $this->redirectToRoute('admin_menus');
        }

        $form = $this->createForm(MenuUnpublishType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setPublishedAt(null);
            $this->menuManager->save($menu);

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
     * @param Request $request
     * @return RedirectResponse
     */
    public function moveAction(Request $request): RedirectResponse
    {
        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('menu-move', $token)) {
            $menu = $this->menuManager->get($request->request->get('menu'));

            $direction = $request->request->get('direction');
            if (!in_array($direction, ['up', 'down'])) {
                return $this->redirectToRoute('admin_menus');
            }

            if ($direction == 'up') {
                if ($menu->getOrder() == 1) {
                    return $this->redirectToRoute('admin_menus');
                }

                $menu2 = $this->menuManager->getPrevious($menu);
            } else {
                $last = $this->menuManager->getLast();
                if ($menu === $last) {
                    return $this->redirectToRoute('admin_menus');
                }

                $menu2 = $this->menuManager->getNext($menu);
            }

            $order = $menu->getOrder();
            $order2 = $menu2->getOrder();

            $menu->setOrder(null);
            $menu2->setOrder(null);
            $this->menuManager->save($menu);
            $this->menuManager->save($menu2);

            $menu->setOrder($order2);
            $menu2->setOrder($order);
            $this->menuManager->save($menu);
            $this->menuManager->save($menu2);
        }

        return $this->redirectToRoute('admin_menus');
    }
}
