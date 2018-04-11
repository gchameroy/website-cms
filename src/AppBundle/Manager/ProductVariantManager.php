<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductVariantManager
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

    public function getNew(Product $product): Product
    {
        return (new Product())
            ->setParent($product)
            ->setVariantName('');
    }

    public function get(int $id, bool $withCheck = true): Product
    {
        /** @var $variant Product */
        $variant = $this->productRepository->find($id);
        if ($withCheck) {
            $this->checkVariant($variant);
        }

        return $variant;
    }

    public function getList(Product $product): array
    {
        return $this->productRepository->findBy([
            ['parent' => $product]
        ]);
    }

    public function save(Product $variant): Product
    {
        $this->entityManager->persist($variant);
        $this->entityManager->flush();

        return $variant;
    }

    public function remove(?Product $variant): void
    {
        if (!$variant) {
            return;
        }

        $this->entityManager->remove($variant);
        $this->entityManager->flush();
    }

    public function removePrices(Product $variant): Product
    {
        foreach ($variant->getPrices() as $price) {
            $this->entityManager->remove($price);
        }
        $this->entityManager->flush();

        return $variant;
    }

    private function checkVariant(?Product $variant): void
    {
        if (!$variant) {
            throw new NotFoundHttpException('Product Variant Not Found.');
        }
    }
}
