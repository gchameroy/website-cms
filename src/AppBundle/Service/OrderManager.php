<?php

namespace AppBundle\Service;

use AppBundle\Entity\Cart;
use AppBundle\Entity\Order;
use AppBundle\Entity\Product;
use AppBundle\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OrderManager
 * @package AppBundle\Service
 */
class OrderManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findAll()
    {
        return $this->em
            ->getRepository(Order::class)
            ->findAll();
    }

    public function getTotal()
    {
        $orders = $this->em
            ->getRepository(Order::class)
            ->findAll();

        $total = 0;
        foreach ($orders as $order) {
            $total += $order->getTotal();
        }

        return $total;
    }
}
