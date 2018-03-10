<?php
namespace AppBundle\Manager;

use AppBundle\Entity\Partner;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Repository\PartnerRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PartnerManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var PartnerRepository */
    private $partnerRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->partnerRepository = $this->entityManager->getRepository(Partner::class);
    }

    public function get(int $id): Partner
    {
        /** @var $partner Partner*/
        $partner = $this->partnerRepository->find($id);
        $this->checkPartner($partner);

        return $partner;
    }

    public function getAll(): array
    {
        return $this->partnerRepository->findAll();
    }

    public function getNew(): Partner
    {
        return new Partner();
    }

    public function save(Partner $partner): Partner
    {
        $this->entityManager->persist($partner);
        $this->entityManager->flush();

        return $partner;
    }

    public function remove (Partner $partner)
    {
        $this->entityManager->remove($partner);
        $this->entityManager->flush();
    }

    private function checkPartner(?Partner $partner)
    {
        if (!$partner) {
            throw new NotFoundHttpException('Partner Not Found.');
        }
    }
}
