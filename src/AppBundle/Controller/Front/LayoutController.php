<?php

namespace AppBundle\Controller\Front;

use AppBundle\Service\CartManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LayoutController extends Controller
{
    /**
     * @return Response
     */
    public function menuAction() {
        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findAll();

        return $this->render('front/layout/menu.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @param int $max
     * @return Response
     */
    public function newslettersAction($max = 2) {
        $newsletters = $this->getDoctrine()
            ->getRepository('AppBundle:Newsletter')
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
}
