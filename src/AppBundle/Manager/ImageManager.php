<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Image;
use AppBundle\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;

class ImageManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ImageRepository */
    private $imageRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->imageRepository = $this->entityManager->getRepository(Image::class);
    }

    public function getNew(): Image
    {
        return new Image();
    }

    public function save(Image $image): Image
    {
        $this->entityManager->persist($image);
        $this->entityManager->flush();
    }

    public function remove(?Image $image): void
    {
        if (!$image) {
            return;
        }

        $this->entityManager->remove($image);
        $this->entityManager->flush();
    }
}
