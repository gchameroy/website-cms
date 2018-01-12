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
     * @Route("/{category}/page-{page}", name="front_products")
     * @Method({"GET"})
     * @param string $category
     * @param int $page
     * @return Response
     */
    public function listAction(string $category, int $page) {
        /** @var Category $category */
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBySlug($category);
        $this->checkCategory($category);

        if ($page <= 0) {
            return $this->redirectToRoute('front_products', [
                'category' => $category->getSlug(),
                'page' => 1
            ]);
        }

        $nbPage = $this->getDoctrine()
            ->getRepository(Product::class)
            ->countNbPagePublishedByCategory($category);

        if ($page > $nbPage) {
            return $this->redirectToRoute('front_products', [
                'category' => $category->getSlug(),
                'page' => $nbPage
            ]);
        }

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findPublishedByCategory($category, $page);

        return $this->render('front/product/list.html.twig', [
            'category' => $category,
            'products' => $products,
            'page' => $page,
            'nbPage' => $nbPage
        ]);
    }

    /**
     * @Route("/categories/{category}/{product}", name="front_product")
     * @Method({"GET"})
     * @param string $category
     * @param string $product
     * @return Response
     */
    public function viewAction(string $category, string $product) {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBySlug($category);
        $this->checkCategory($category);

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
     *@param $product
     */
    private function checkProduct($product) {
        if (!$product) {
            throw $this->createNotFoundException('Product Not Found.');
        }
    }
}
