<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CartRepository extends EntityRepository
{
    public function findOneByToken($token)
    {
        return $this->createQueryBuilder('c')
            ->where('c.token = :token')
                ->setParameter('token', $token)
            ->andWhere('c.orderedAt is null')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
