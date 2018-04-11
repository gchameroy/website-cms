<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MenuRepository extends EntityRepository
{
    public function getLast()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.order', 'desc')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPublished()
    {
        return $this->createQueryBuilder('m')
            ->where('m.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('m.order', 'asc')
            ->getQuery()
            ->getResult();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.order', 'asc')
            ->getQuery()
            ->getResult();
    }
}
