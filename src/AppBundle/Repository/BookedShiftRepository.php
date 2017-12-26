<?php

namespace AppBundle\Repository;

use Doctrine\ORM\NonUniqueResultException;

/**
 * BookedShiftRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookedShiftRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $shift
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findSoonestDismissed($shift)
    {
        $qb = $this->createQueryBuilder('s');

        $qb
            ->where('s.shift = :shift')
            ->andWhere('s.isDismissed = 1')
            ->setParameter('shift', $shift)
            ->orderBy('s.dismissedTime', 'ASC')
            ->setMaxResults(1);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }
}
