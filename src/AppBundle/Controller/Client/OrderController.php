<?php
/**
 * Created by PhpStorm.
 * User: delphine
 * Date: 10/10/17
 * Time: 18:17
 */

namespace AppBundle\Controller\Client;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 */
class OrderController extends Controller
{
    /**
     * @Route("/", name="client_order_add")
     * @return Response
     */
    public function listAction()
    {
        return $this->render('client/order/add.html.twig');
    }
}
