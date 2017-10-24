<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Attribute;
use AppBundle\Entity\CartProduct;
use AppBundle\Entity\Product;
use AppBundle\Service\CartManager;
use Doctrine\ORM\EntityRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
        $cartProduct->setProduct($product);
        $cartProduct->setCart($cartManager->getCurrentCart());

        $form = $this->createFormBuilder($cartProduct)
            ->setAction($this->generateUrl('front_cart_product_add', [
                'product' => $product->getId()
            ]))
            ->add('quantity', IntegerType::class)
            ->add('attribute', EntityType::class, [
                'class' => Attribute::class,
                'choice_label' => function (Attribute $attribute) {
                    return $attribute->getLabel();
                },
                'query_builder' => function (EntityRepository $er) use ($product) {
                    return $er->createQueryBuilder('a')
                        ->join('a.products', 'p')
                        ->where('p.id = :product')
                        ->setParameter('product', $product->getId())
                        ->orderBy('a.label', 'ASC');
                }
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $cartProduct2 = $em->getRepository(CartProduct::class)
                ->findOneBy([
                   'cart' => $cartProduct->getCart(),
                   'product' => $cartProduct->getProduct(),
                   'attribute' => $cartProduct->getAttribute(),
                ]);
            if (!$cartProduct2) {
                $em->persist($cartProduct);
            } else {
                $cartProduct2->setQuantity(
                    $cartProduct2->getQuantity() + 1
                );
                $em->persist($cartProduct2);
            }
            dump($cartProduct);
            dump($cartProduct2);

            $em->flush();

            $this->addFlash('success', 'Product added successfully to the cart.');

            return $this->redirectToRoute('front_cart');
        }

        return $this->render('front/cart-product/partial/add.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     *@param $product
     */
    private function checkProduct($product) {
        if (!$product) {
            throw $this->createNotFoundException('Product Not Found.');
        }
    }
}
