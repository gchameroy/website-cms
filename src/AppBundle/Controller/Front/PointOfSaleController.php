<?php

namespace AppBundle\Controller\Front;

use AppBundle\Manager\PointOfSaleManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PointOfSaleController extends Controller
{
    /** @var PointOfSaleManager */
    private $pointOfSaleManager;

    public function __construct(PointOfSaleManager $pointOfSaleManager)
    {
        $this->pointOfSaleManager = $pointOfSaleManager;
    }

    /**
     * @Route("/points-de-ventes", name="front_point_of_sales")
     * @Method({"GET"})
     * @return Response
     */
    public function listAction(): Response
    {
        $pointOfSales = $this->pointOfSaleManager->getList();

        return $this->render('front/point-of-sale/list.html.twig', [
            'pointOfSales' => $pointOfSales
        ]);
    }
}
