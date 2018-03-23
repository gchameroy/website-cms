<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Newsletter;
use AppBundle\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsletterManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var NewsletterRepository */
    private $newsletterRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->newsletterRepository = $this->entityManager->getRepository(Newsletter::class);
    }

    public function get(int $id): Newsletter
    {
        /** @var $newsletter Newsletter */
        $newsletter = $this->newsletterRepository->find($id);
        $this->checkNewsletter($newsletter);

        return $newsletter;
    }

    public function getPublishedBySlug(string $slug): Newsletter
    {
        $newsletter = $this->newsletterRepository->getPublishedBySlug($slug);
        $this->checkNewsletter($newsletter);

        return $newsletter;
    }

    public function getList(): array
    {
        return $this->newsletterRepository->findBy(
            [],
            ['publishedAt' => 'desc']
        );
    }

    public function getListPublishedByPage(?int $page = 1): array
    {
        return $this->newsletterRepository->getAllPublishedByPage($page);
    }

    public function getNbPagePublished(): int
    {
        return $this->newsletterRepository->getNbPagePublished();
    }

    public function getNew(): Newsletter
    {
        return new Newsletter();
    }

    public function save(Newsletter $newsletter): Newsletter
    {
        $this->entityManager->persist($newsletter);
        $this->entityManager->flush();

        return $newsletter;
    }

    public function remove(Newsletter $newsletter)
    {
        $this->entityManager->remove($newsletter);
        $this->entityManager->flush();
    }

    public function removeOldImage(Newsletter $newsletter)
    {
        $oldImage = $newsletter->getImage();
        if ($oldImage) {
            $newsletter->setImage(null);
            $this->entityManager->persist($newsletter);
            $this->entityManager->remove($oldImage);
            $this->entityManager->flush();
        }

        return $newsletter;
    }

    private function checkNewsletter(?Newsletter $newsletter)
    {
        if (!$newsletter) {
            throw new NotFoundHttpException('Newsletter Not Found.');
        }
    }
}
