<?php
namespace AppBundle\Manager;

use AppBundle\Entity\DeliveryZone;
use AppBundle\Repository\DeliveryZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeliveryZoneManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DeliveryZoneRepository */
    private $deliveryZoneRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->deliveryZoneRepository = $this->entityManager
            ->getRepository(DeliveryZone::class);
    }

    public function getList(): array
    {
        return $this->deliveryZoneRepository->findBy([
            'deletedAt' => null
        ]);
    }

    public function get(int $deliveryZoneId): DeliveryZone
    {
        /** @var $deliveryZone DeliveryZone*/
        $deliveryZone = $this->deliveryZoneRepository->find($deliveryZoneId);
        $this->checkDeliveryZone($deliveryZone);

        return $deliveryZone;
    }

    public function getNew(): DeliveryZone
    {
        return new DeliveryZone();
    }

    public function saveNew(DeliveryZone $deliveryZone): DeliveryZone
    {
        $this->entityManager->persist($deliveryZone);
        $this->entityManager->flush();

        return $deliveryZone;
    }

    public function save(DeliveryZone $deliveryZone): ?DeliveryZone
    {
        $this->entityManager->detach($deliveryZone);

        $newDeliveryZone = clone($deliveryZone);
        $this->entityManager->persist($newDeliveryZone);
        $this->entityManager->flush();

        $oldDeliveryZone = $this->get($deliveryZone->getId());
        $oldDeliveryZone->setDeletedAt(new \DateTime());
        $this->entityManager->persist($oldDeliveryZone);
        $this->entityManager->flush();

        return null;
    }

    public function remove (DeliveryZone $deliveryZone)
    {
        $deliveryZone->setDeletedAt(new \DateTime());
        $this->entityManager->persist($deliveryZone);
        $this->entityManager->flush();
    }

    private function checkDeliveryZone(?DeliveryZone $deliveryZone)
    {
        if (!$deliveryZone) {
            throw new NotFoundHttpException('Delivery Zone Not Found.');
        }
    }
}
