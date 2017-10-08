<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\CartProduct;
use AppBundle\Service\CartManager;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="front_cart")
     * @Method({"GET"})
     * @param CartManager $cartManager
     * @return Response
     */
    public function viewAction(CartManager $cartManager) {
        return $this->render('front/cart/view.html.twig', [
            'cart' => $cartManager->getCurrentCart()
        ]);
    }

    /**
     * @Route("/cart/{cartProduct}/add-one", name="front_cart_add_one_quantity")
     * @Method({"GET"})
     * @param int $cartProduct
     * @param CartManager $cartManager
     * @param EntityManager $em
     * @return Response
     */
    public function addOneProductAction(int $cartProduct, CartManager $cartManager, EntityManager $em)
    {
        $cart = $cartManager->getCurrentCart();
        $cartProduct = $em->getRepository(CartProduct::class)
            ->findOneBy([
                'cart' => $cart,
                'id' => $cartProduct
            ]);
        if (!$cartProduct) {
            return $this->redirectToRoute('front_cart');
        }

        $cartProduct->setQuantity(
            $cartProduct->getQuantity() + 1
        );
        $em->persist($cartProduct);
        $em->flush();

        return $this->redirectToRoute('front_cart');
    }

    /**
     * @Route("/cart/{cartProduct}/remove-one", name="front_cart_remove_one_quantity")
     * @Method({"GET"})
     * @param int $cartProduct
     * @param CartManager $cartManager
     * @param EntityManager $em
     * @return Response
     */
    public function removeOneProductAction(int $cartProduct, CartManager $cartManager, EntityManager $em)
    {
        $cart = $cartManager->getCurrentCart();
        $cartProduct = $em->getRepository(CartProduct::class)
            ->findOneBy([
                'cart' => $cart,
                'id' => $cartProduct
            ]);
        if (!$cartProduct) {
            return $this->redirectToRoute('front_cart');
        }

        if ($cartProduct->getQuantity() > 1) {
            $cartProduct->setQuantity(
                $cartProduct->getQuantity() - 1
            );
            $em->persist($cartProduct);
        } else {
            $em->remove($cartProduct);
        }
        $em->flush();

        return $this->redirectToRoute('front_cart');
    }
}
