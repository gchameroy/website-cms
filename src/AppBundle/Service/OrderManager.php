<?php

namespace AppBundle\Service;

use AppBundle\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

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
