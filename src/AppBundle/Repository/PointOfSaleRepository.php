<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PointOfSaleRepository extends EntityRepository
{
    public function findAll()
    {
        return $this->createQueryBuilder('pos')
            ->innerJoin('pos.address', 'a')
            ->orderBy('a.company', 'asc')
            ->getQuery()
            ->getResult();
    }
}
