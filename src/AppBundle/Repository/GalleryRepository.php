<?php

namespace AppBundle\Repository;

class GalleryRepository extends PublishableEntityRepository
{
    /**
     * @param int $perPage
     * @return int
     */
    public function countNbPagePublished(int $perPage)
    {
        $nb = $this->createQueryBuilder('g')
            ->select('count(g.id)')
            ->where('g.publishedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();

        $nbPage = ceil($nb / $perPage);
        if ($nbPage == 0) {
            $nbPage = 1;
        }

        return $nbPage;
    }
}
