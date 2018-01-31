<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\PointOfSale;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PointOfSaleController extends Controller
{
    /**
     * @Route("/points-de-ventes", name="front_point_of_sales")
     * @Method({"GET"})
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function listAction(EntityManagerInterface $entityManager) {

        $pointOfSales = $entityManager
            ->getRepository(PointOfSale::class)
            ->findAll();

        return $this->render('front/point-of-sale/list.html.twig', [
            'pointOfSales' => $pointOfSales
        ]);
    }
}
