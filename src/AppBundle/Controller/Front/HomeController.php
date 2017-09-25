<?php

namespace AppBundle\Controller\Front;

use Doctrine\ORM\EntityManagerInterface;
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
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function productsAction($max = 3, EntityManagerInterface $em) {
        $products = $em->getRepository('AppBundle:Product')
            ->findLasts($max);

        return $this->render('front/home/partial/products.html.twig', [
            'products' => $products
        ]);
    }
}
