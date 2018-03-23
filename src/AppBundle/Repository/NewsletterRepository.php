<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Newsletter;
use Doctrine\ORM\EntityRepository;

class NewsletterRepository extends EntityRepository
{
    const PER_PAGE = 3;

    /**
     * @param int|null $page
     * @return array
     */
    public function getListPublishedByPage(?int $page = 1): array
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

    public function getNbPagePublished(): int
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

    public function getPublishedBySlug(string $slug): ?Newsletter
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
