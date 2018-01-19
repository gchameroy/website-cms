<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\CartProduct;
use AppBundle\Entity\Product;
use AppBundle\Form\Type\Cart\CartProductType;
use AppBundle\Service\CartManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartProductController extends Controller
{
    /**
     * @Route("/products/{product}/add-to-cart", name="front_cart_product_add")
     * @Method({"GET","POST"})
     * @param int $product
     * @param Request $request
     * @param CartManager $cartManager
     * @return Response
     */
    public function addAction($product, Request $request, CartManager $cartManager)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($product);
        $this->checkProduct($product);

        $cartProduct = new CartProduct();

        $form = $this->createForm(CartProductType::class, $cartProduct, [
            'data' => [
                'product' => $product,
                'offer' => $this->getUser() ? $this->getUser()->getOffer() : null
            ],
            'action' => $this->generateUrl('front_cart_product_add', [
                'product' => $product->getId()
            ])
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cartProduct->setCart($cartManager->getCurrentCart());
            $variant = $this->getDoctrine()
                ->getRepository(Product::class)
                ->find($request->request->get('cart_product')['product']);
            $this->checkProduct($variant);
            $cartProduct->setProduct($variant);

            $em = $this->getDoctrine()->getManager();
            $em->persist($cartProduct);
            $em->flush();

            $this->addFlash('success', 'Product added successfully to the cart.');

            return $this->redirectToRoute('front_cart');
        }

        return $this->render('front/cart-product/partial/add.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param $product
     */
    private function checkProduct($product)
    {
        if (!$product) {
            throw $this->createNotFoundException('Product Not Found.');
        }
    }
}
