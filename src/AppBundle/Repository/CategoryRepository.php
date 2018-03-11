<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function findLast()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.position', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.position', 'asc')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function findPublished()
    {
        return $this->createQueryBuilder('c')
            ->where('c.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('c.position', 'asc')
            ->getQuery()
            ->getResult();
    }
}
