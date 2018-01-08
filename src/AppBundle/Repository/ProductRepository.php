<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
    const PER_PAGE = 9;

    public function findAll()
    {
        return $this->createQueryBuilder('p')
            ->where('p.parent is null')
            ->getQuery()
            ->getResult();
    }

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

    public function findPublishedByCategory(Category $category, $page = 1)
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

    public function findLastPublished($max = 10)
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
}
