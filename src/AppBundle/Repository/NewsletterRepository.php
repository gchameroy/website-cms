<?php

namespace AppBundle\Repository;

class NewsletterRepository extends PublishableEntityRepository
{
    const PER_PAGE = 3;

    /**
     * @param int|null $page
     * @return array
     */
    public function findPublishedByPage(?int $page = 1)
    {
        return $this->createQueryBuilder('n')
            ->where('n.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->orderBy('n.publishedAt', 'desc')
            ->setFirstResult(($page - 1) * self::PER_PAGE)
            ->setMaxResults(self::PER_PAGE)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int
     */
    public function countNbPagePublished()
    {
        $nb = $this->createQueryBuilder('n')
            ->select('count(n.id)')
            ->where('n.publishedAt <= :now')
                ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getSingleScalarResult();

        $nbPage = ceil($nb / self::PER_PAGE);

        return $nbPage == 0 ? 1 : $nbPage;
    }
}
