<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/orders")
 */
class OrderController extends Controller
{
    /**
     * @Route("/", name="admin_orders")
     * @return Response
     */
    public function listAction() {
        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->findAll();

        return $this->render('admin/order/list.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/{id}", name="admin_order", requirements={"id": "\d+"})
     * @Method({"GET"})
     * @param integer $id
     * @return Response
     */
    public function viewAction($id) {
        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($id);
        $this->checkOrder($order);

        return $this->render('admin/order/view.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @param $order
     */
    private function checkOrder($order) {
        if (!$order) {
            throw $this->createNotFoundException('Order Not Found.');
        }
    }
}
