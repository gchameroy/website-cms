<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    const PER_PAGE = 9;

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->where('p.parent is null')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findPublished()
    {
        return $this->createQueryBuilder('p')
            ->where('p.publishedAt <= :now')
            ->andWhere('p.parent is null')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.publishedAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $slug
     * @return mixed
     */
    public function findOnePublished(string $slug)
    {
        return $this->createQueryBuilder('p')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.slug = :slug')
                ->setParameter('slug', $slug)
            ->andWhere('p.parent is null')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Category $category
     * @param int|null $page
     * @return array
     */
    public function findPublishedByCategory(Category $category, ?int $page = 1)
    {
        return $this->createQueryBuilder('p')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.category = :category')
                ->setParameter('category', $category)
            ->andWhere('p.parent is null')
            ->orderBy('p.publishedAt', 'desc')
            ->setFirstResult(($page - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Category $category
     * @return int
     */
    public function countNbPagePublishedByCategory(category $category)
    {
        $nb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.category = :category')
                ->setParameter('category', $category)
            ->andWhere('p.parent is null')
            ->getQuery()
            ->getSingleScalarResult();

        $nbPage = ceil($nb / self::PER_PAGE);
        if ($nbPage == 0) {
            $nbPage = 1;
        }

        return $nbPage;
    }

    /**
     * @param int|null $max
     * @return array
     */
    public function findLastPublished(?int $max = 10)
    {
        return $this->createQueryBuilder('p')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.parent is null')
            ->orderBy('p.publishedAt', 'desc')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $category
     * @param int|null $max
     * @return array
     */
    public function findLastPublishedByCategory(string $category, ?int $max = 10)
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.category', 'c')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.parent is null')
            ->andWhere('c.slug = :category')
            ->setParameter('category', $category)
            ->orderBy('p.publishedAt', 'desc')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Product $product
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findPublishedVariants(Product $product)
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :product')
            ->orWhere('p.parent = :product')
            ->setParameter('product', $product);
    }
}
