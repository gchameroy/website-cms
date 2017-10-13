<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="front_home")
     * @Method({"GET"})
     * @return Response
     */
    public function homeAction() {
        return $this->render('front/home/home.html.twig');
    }

    /**
     * @param int $max
     * @return Response
     */
    public function productsAction($max = 3) {
        $products = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->findLastPublished($max);

        return $this->render('front/layout/partial/last-products.html.twig', [
            'products' => $products
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
}
