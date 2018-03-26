<?php

namespace AppBundle\Manager;

use AppBundle\Entity\PointOfSale;
use AppBundle\Repository\PointOfSaleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PointOfSaleManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PointOfSaleRepository */
    private $pointOfSaleRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->pointOfSaleRepository = $this->entityManager->getRepository(PointOfSale::class);
    }

    public function get(int $id): PointOfSale
    {
        /** @var $pointOfSale PointOfSale */
        $pointOfSale = $this->pointOfSaleRepository->find($id);
        $this->checkPointOfSale($pointOfSale);

        return $pointOfSale;
    }

    public function getList(): array
    {
        return $this->pointOfSaleRepository->findAll();
    }

    public function getNew(): PointOfSale
    {
        return new PointOfSale();
    }

    public function save(PointOfSale $pointOfSale): PointOfSale
    {
        $this->entityManager->persist($pointOfSale);
        $this->entityManager->flush();

        return $pointOfSale;
    }

    public function remove(PointOfSale $pointOfSale): void
    {
        $this->entityManager->remove($pointOfSale);
        $this->entityManager->flush();
    }

    private function checkPointOfSale(?PointOfSale $pointOfSale)
    {
        if (!$pointOfSale) {
            throw new NotFoundHttpException('Point Of Sale Not Found.');
        }
    }
}
