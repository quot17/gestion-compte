<?php

namespace AppBundle\Repository;

/**
 * BeneficiaryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BeneficiaryRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * findOneFromAutoComplete
     *
     * We consider that the $beneficiary has the following format:
     * "#<Membership.member_number> <Beneficiary.firstname> <Beneficiary.lastname>"
     */
    public function findOneFromAutoComplete($beneficiary)
    {
        $qb = $this->createQueryBuilder('b');

        $qb->leftJoin('b.membership', 'm')
            ->where('CONCAT(\'#\', m.member_number, \' \', b.firstname, \' \', b.lastname) = :fullname')
            ->setParameter('fullname', $beneficiary);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * findFromAutoComplete
     *
     * We consider that each string of $beneficiaries has the following format:
     * "#<Membership.member_number> <Beneficiary.firstname> <Beneficiary.lastname>"
     */
    public function findFromAutoComplete($beneficiaries)
    {
        $qb = $this->createQueryBuilder('b');

        $qb->leftJoin('b.membership', 'm')
            ->where('CONCAT(\'#\', m.member_number, \' \', b.firstname, \' \', b.lastname) IN (:fullname)')
            ->setParameter('fullname', $beneficiaries);

        return $qb->getQuery()->getResult();
    }

    /**
     * findAllActive
     *
     * return all the active beneficiaries meaning with
     * an active membership
     */
    public function findAllActive()
    {

        $qb = $this->createQueryBuilder('beneficiary');

        $qb->select('beneficiary, membership')
            ->join('beneficiary.user', 'user')
            ->join('beneficiary.membership', 'membership')
            ->where('membership.withdrawn = 0');

        return $qb->getQuery()->getResult();
    }

    public function findCoShifters($shift)
    {
        $qb = $this->createQueryBuilder('b')
                   ->leftJoin('b.shifts', 's')
                    ->where('s.start = :start')
                    ->andWhere('s.end = :end')
                    ->andWhere('s.job = :job')
                    ->andWhere('s.id != :id')
                    ->andWhere('s.shifter IS NOT NULL')
                    ->setParameter('start', $shift->getStart())
                    ->setParameter('end', $shift->getEnd())
                    ->setParameter('job', $shift->getJob())
                    ->setParameter('id', $shift->getId());

        return $qb
            ->getQuery()
            ->getResult();

    }
}
