<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends Controller
{
    /**
     * @Route("/categories/{category}-{page}", name="front_products")
     * @Method({"GET"})
     * @param int $category
     * @param int $page
     * @return Response
     */
    public function listAction($category, $page) {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($category);
        $this->checkCategory($category);

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findPublishedByCategory($category, $page);

        $nbPage = $this->getDoctrine()
            ->getRepository(Product::class)
            ->countNbPagePublishedByCategory($category);

        return $this->render('front/product/list.html.twig', [
            'category' => $category,
            'products' => $products,
            'page' => $page,
            'nbPage' => $nbPage
        ]);
    }

    /**
     * @Route("/products/{product}", name="front_product")
     * @Method({"GET"})
     * @param int $product
     * @return Response
     */
    public function viewAction($product) {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findOnePublished($product);
        $this->checkProduct($product);

        return $this->render('front/product/view.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @param $category
     */
    private function checkCategory($category) {
        if (!$category) {
            throw $this->createNotFoundException('Category Not Found.');
        }
    }

    /**
     * @param $product
     */
    private function checkProduct($product) {
        if (!$product) {
            throw $this->createNotFoundException('Product Not Found.');
        }
    }
}
