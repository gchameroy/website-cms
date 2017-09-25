<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCategoryController extends Controller
{
    /**
     * @Route("/categories", name="front_product_categories")
     * @Method({"GET"})
     * @return Response
     */
    public function productCategoriesAction() {
        $categories = $this->getDoctrine()
            ->getRepository('AppBundle:Category')
            ->findAll();

        return $this->render('front/category/list.html.twig', [
            'categories' => $categories
        ]);
    }
}
