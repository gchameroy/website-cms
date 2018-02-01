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
use AppBundle\Service\MenuManager;
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
    /**
     * @Route("/", name="admin_menus")
     * @return Response
     */
    public function listAction() {
        $menus = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->findAll(0, 0, 'asc', false);

        return $this->render('admin/menu/list.html.twig', [
            'menus' => $menus
        ]);
    }

    /**
     * @Route("/add", name="admin_menus_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param MenuManager $menuManager
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, MenuManager $menuManager) {
        $em = $this->getDoctrine()->getManager();
        $menu = new Menu();

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setOrder($menuManager->getNextOrder());
            $em->persist($menu);
            $em->flush();

            $this->addFlash('success', 'Menu added successfully.');

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
     * @Route("/{id}/edit", name="admin_menu_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $menu = $em->getRepository(Menu::class)
            ->find($id);
        $this->checkMenu($menu);

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            $this->addFlash('success', 'Menu edited successfully.');

            return $this->redirectToRoute('admin_menus', [
                'id' => $id,
            ]);
        }

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
     * @Route("/{id}/delete", name="admin_menu_delete", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function deleteAction($id, Request $request) {
        $menu = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->find($id);
        $this->checkMenu($menu);

        $form = $this->createForm(MenuDeleteType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($menu);
            $em->flush();

            $this->addFlash('success', 'Menu deleted successfully.');

            return $this->redirectToRoute('admin_menus', [
                'id' => $id,
            ]);
        }

        return $this->render('admin/menu/delete.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu
        ]);
    }

    /**
     * @Route("/{id}/publish", name="admin_menu_publish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function publishAction(Request $request, $id) {
        $menu = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->find($id);
        $this->checkMenu($menu);
        $menu->setPublishedAt(new \DateTime());

        $form = $this->createForm(MenuPublishType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            $this->addFlash('success', 'Menu published successfully.');

            return $this->redirectToRoute('admin_menus');
        }

        return $this->render('admin/menu/publish.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    /**
     * @Route("/{id}/unpublish", name="admin_menu_unpublish", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function unpublishAction(Request $request, $id) {
        $menu = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->find($id);
        $this->checkMenu($menu);

        if (null === $menu->getPublishedAt()) {
            return $this->redirectToRoute('admin_menus');
        }

        $form = $this->createForm(MenuUnpublishType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setPublishedAt(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();

            $this->addFlash('success', 'Menu unpublished successfully.');

            return $this->redirectToRoute('admin_menus');
        }

        return $this->render('admin/menu/unpublish.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu,
        ]);
    }

    /**
     * @param $menu
     */
    private function checkMenu($menu) {
        if (!$menu) {
            throw $this->createNotFoundException('Menu Not Found.');
        }
    }
}
