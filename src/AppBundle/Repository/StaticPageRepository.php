<?php

namespace AppBundle\Repository;

use AppBundle\Entity\StaticPage;
use Doctrine\ORM\EntityRepository;

class StaticPageRepository extends EntityRepository
{
    public function getPublishedBySlug(string $slug): ?StaticPage
    {
        return $this->createQueryBuilder('n')
            ->where('n.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->andWhere('n.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
