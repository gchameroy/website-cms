<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PublishableEntityRepository extends EntityRepository
{
    /**
     * @param int $perPage
     * @param int $page
     * @param string $order
     * @param bool $published
     * @return array
     */
    public function findAll($perPage = 10, $page = 1, $order = 'asc', $published = true)
    {
        $qb = $this->createQueryBuilder('e');

        if ($published) {
            $qb->where('e.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
                ->orderBy('e.publishedAt', $order);
        } else {
            $qb->orderBy('e.id', $order);
        }

        if ($perPage > 0) {
            $qb->setFirstResult(($page - 1) * $perPage)
                ->setMaxResults($perPage);
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param string $slug
     * @return mixed
     */
    public function findOnePublished(string $slug)
    {
        return $this->createQueryBuilder('e')
            ->where('e.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->andWhere('e.slug = :slug')
                ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array
     */
    public function findPublished()
    {
        return $this->createQueryBuilder('e')
            ->where('e.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('e.publishedAt', 'desc')
            ->getQuery()
            ->getResult();
    }
}
