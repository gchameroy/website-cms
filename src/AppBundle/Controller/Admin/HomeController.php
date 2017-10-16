<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Service\OrderManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="admin_home")
     * @Method({"GET"})
     * @param OrderManager $orderManager
     * @return Response
     */
    public function homeAction(OrderManager $orderManager) {
        $orders = $orderManager->findAll();
        $total = $orderManager->getTotal();

        return $this->render('admin/home.html.twig', [
            'orders' => $orders,
            'total' => $total
        ]);
    }
}
