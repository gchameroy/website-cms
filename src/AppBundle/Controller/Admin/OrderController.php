<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Manager\OrderManager;
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
    /** @var OrderManager */
    private $orderManager;

    public function __construct(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    /**
     * @Route("/", name="admin_orders")
     * @return Response
     */
    public function listAction() {
        $orders = $this->orderManager->getList();

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
    public function viewAction(int $id) {
        $order = $this->orderManager->get($id);

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

        $order = $this->orderManager->get($order_id);

        $order->setIsPaid($paid);
        $this->orderManager->save($order);

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
        $order = $this->orderManager->get($order_id);

        $order->setStatus($status);
        $this->orderManager->save($order);

        return new Response('', Response::HTTP_OK);
    }
}
