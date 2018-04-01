<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Gallery;
use AppBundle\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GalleryManager
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var GalleryRepository */
    private $galleryRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->galleryRepository = $entityManager->getRepository(Gallery::class);
    }

    public function get(int $id): Gallery
    {
        /** @var $gallery Gallery */
        $gallery = $this->galleryRepository->find($id);
        $this->checkGallery($gallery);

        return $gallery;
    }

    public function getPublished(int $page = 1): array
    {
        return $this->galleryRepository->findAll(11, $page, 'desc');
    }

    public function getNbPages(): int
    {
        return $this->galleryRepository->countNbPagePublished(11);
    }

    public function getList(): array
    {
        return $this->galleryRepository->findAll(0, null, 'desc', false);
    }

    public function save(Gallery $gallery): Gallery
    {
        $this->entityManager->persist($gallery);
        $this->entityManager->flush();

        return $gallery;
    }

    public function remove(?Gallery $gallery): void
    {
        if (!$gallery) {
            return;
        }

        $this->entityManager->remove($gallery);
        $this->entityManager->flush();
    }

    private function checkGallery(?Gallery $gallery): void
    {
        if (!$gallery) {
            throw new NotFoundHttpException('Gallery Not Found.');
        }
    }
}
