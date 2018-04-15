<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\OrderRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var OrderRepository */
    private $orderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $this->entityManager->getRepository(Order::class);
    }

    public function getList(): array
    {
        return $this->orderRepository->findAll();
    }

    public function get(int $id): Order
    {
        /** @var $order Order*/
        $order = $this->orderRepository->find($id);
        $this->checkOrder($order);

        return $order;
    }

    public function save(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    public function getTotal(): float
    {
        $orders = $this->entityManager
            ->getRepository(Order::class)
            ->findAll();

        $total = 0;
        foreach ($orders as $order) {
            /** @var Order $order */
            $total += $order->getTotal();
        }
        return $total;
    }

    private function checkOrder(?Order $order): void
    {
        if (!$order) {
            throw new NotFoundHttpException('Order Not Found.');
        }
    }
}
