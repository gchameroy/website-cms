<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductCategoryController extends Controller
{
    /**
     * @Route("/categories/{slug}", name="front_category")
     * @Method({"GET"})
     * @param string $slug
     * @return Response
     */
    public function viewAction(string $slug)
    {
        return $this->redirectToRoute('front_products', [
            'category' => $slug,
            'page' => 1
        ]);
    }
}
