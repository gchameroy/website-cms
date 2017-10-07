<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    const PER_PAGE = 6;

    public function findOnePublished($product)
    {
        return $this->createQueryBuilder('p')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.id = :product')
                ->setParameter('product', $product)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPublishedByCategory(Category $category, $page = 1)
    {
        return $this->createQueryBuilder('p')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.category = :category')
                ->setParameter('category', $category)
            ->orderBy('p.publishedAt', 'desc')
            ->setFirstResult(($page - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE)
            ->getQuery()
            ->getResult();
    }

    public function countNbPagePublishedByCategory(category $category)
    {
        $nb = $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('p.category = :category')
                ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();

        return ceil($nb / self::PER_PAGE);
    }

    public function findLastPublished($max = 10)
    {
        return $this->createQueryBuilder('p')
            ->where('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->orderBy('p.publishedAt', 'desc')
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }
}
