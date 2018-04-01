<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Category;
use AppBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $this->entityManager->getRepository(Category::class);
    }

    public function get(int $id): Category
    {
        /** @var $category Category */
        $category = $this->categoryRepository->find($id);
        $this->checkCategory($category);

        return $category;
    }

    public function getList(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function getOneByPosition(int $position): Category
    {
        return $this->categoryRepository->findOneByPosition($position);
    }

    public function getLast(): ?Category
    {
        return $this->categoryRepository->findLast();
    }

    public function getNextPosition(): int
    {
        $lastCategory = $this->categoryRepository->findLast();
        $position = $lastCategory ? $lastCategory->getPosition() + 1 : 1;

        return $position;
    }

    public function save(Category $category): Category
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function remove(?Category $category): void
    {
        if (!$category) {
            return;
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }

    private function checkCategory(?Category $category): void
    {
        if (!$category) {
            throw new NotFoundHttpException('Category Not Found.');
        }
    }
}
