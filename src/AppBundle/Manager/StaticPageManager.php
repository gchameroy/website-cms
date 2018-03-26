<?php

namespace AppBundle\Manager;

use AppBundle\Entity\StaticPage;
use AppBundle\Repository\StaticPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StaticPageManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var StaticPageRepository */
    private $staticPageRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->staticPageRepository = $this->entityManager->getRepository(StaticPage::class);
    }

    public function get(int $id): StaticPage
    {
        /** @var $staticPage StaticPage */
        $staticPage = $this->staticPageRepository->find($id);
        $this->checkStaticPage($staticPage);

        return $staticPage;
    }

    public function getPublishedBySlug(string $slug): StaticPage
    {
        $staticPage = $this->staticPageRepository->getPublishedBySlug($slug);
        $this->checkStaticPage($staticPage);

        return $staticPage;
    }

    public function getList(): array
    {
        return $this->staticPageRepository->findBy(
            [],
            ['publishedAt' => 'desc']
        );
    }

    public function getNew(): StaticPage
    {
        return new StaticPage();
    }

    public function save(StaticPage $staticPage): StaticPage
    {
        $this->entityManager->persist($staticPage);
        $this->entityManager->flush();

        return $staticPage;
    }

    public function remove(StaticPage $staticPage): void
    {
        $this->entityManager->remove($staticPage);
        $this->entityManager->flush();
    }

    private function checkStaticPage(?StaticPage $staticPage)
    {
        if (!$staticPage) {
            throw new NotFoundHttpException('Static Page Not Found.');
        }
    }
}
