<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductManager
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProductRepository */
    private $productRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $this->entityManager->getRepository(Product::class);
    }

    public function getNew(): Product
    {
        return new Product();
    }

    public function get(int $id, bool $withCheck = true): Product
    {
        /** @var $product Product */
        $product = $this->productRepository->find($id);
        if ($withCheck) {
            $this->checkProduct($product);
        }

        return $product;
    }

    public function getList(): array
    {
        return $this->productRepository->findAll();
    }

    public function save(Product $product): Product
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function remove(?Product $product): void
    {
        if (!$product) {
            return;
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    private function checkProduct(?Product $product): void
    {
        if (!$product) {
            throw new NotFoundHttpException('Product Not Found.');
        }
    }
}
