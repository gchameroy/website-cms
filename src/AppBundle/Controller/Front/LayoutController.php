<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Category;
use AppBundle\Entity\Menu;
use AppBundle\Entity\Newsletter;
use AppBundle\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @return Response
     */
    public function menuAction() {
        $menus = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->findPublished();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findPublished();

        return $this->render('front/layout/partial/menu.html.twig', [
            'menus' => $menus,
            'categories' => $categories
        ]);
    }

    /**
     * @return Response
     */
    public function subMenuAction() {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findPublished();

        return $this->render('front/layout/partial/sub-menu.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @param int $max
     * @return Response
     */
    public function newslettersAction($max = 2) {
        $newsletters = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->findAll($max, 1, 'desc');

        return $this->render('front/home/partial/newsletters.html.twig', [
            'newsletters' => $newsletters
        ]);
    }

    /**
     * @param CartManager $cartManager
     * @return Response
     */
    public function cartAction(CartManager $cartManager) {
        $cart = $cartManager->getCurrentCart();

        return $this->render('front/layout/partial/cart.html.twig', [
            'cart' => $cart
        ]);
    }

    /**
     * @return Response
     */
    public function footerAction() {
        $menus = $this->getDoctrine()
            ->getRepository(Menu::class)
            ->findPublished();

        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findPublished();

        $newsletters = $this->getDoctrine()
            ->getRepository(Newsletter::class)
            ->findAll(6, 1, 'desc');

        return $this->render('front/layout/footer.html.twig', [
            'menus' => $menus,
            'categories' => $categories,
            'newsletters' => $newsletters
        ]);
    }
}
