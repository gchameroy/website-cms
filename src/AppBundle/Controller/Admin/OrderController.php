<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/paid", name="admin_order_paid")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function paidAction(Request $request) {
        $order_id = $request->request->get('order_id');
        $paid = $request->request->get('paid');

        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($order_id);
        $this->checkOrder($order);

        $order->setIsPaid($paid);
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return new Response('', Response::HTTP_OK);
    }

    /**
     * @Route("/status", name="admin_order_status")
     * @Method({"POST"})
     * @param Request $request
     * @return Response
     */
    public function statusAction(Request $request) {
        $status = $request->request->get('status');
        $order_id = $request->request->get('order_id');
        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($order_id);
        $this->checkOrder($order);

        $order->setStatus($status);
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return new Response('', Response::HTTP_OK);
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
