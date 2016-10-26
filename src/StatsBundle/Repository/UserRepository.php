<?php

namespace OC\PlatformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UserRepository
 *
 */
class UserRepository extends EntityRepository
{
    public function getUsers($page, $nbPerPage)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.image', 'i')
            ->addSelect('i')
            ->orderBy('a.date', 'DESC');
        $this->whereIsVisible($qb);

        $query = $qb->getQuery()
            ->setFirstResult(($page-1) * $nbPerPage)
            ->setMaxResults($nbPerPage);

        return new Paginator($query, true);
    }

    public function whereIsVisible(QueryBuilder $qb)
    {
    }
}
