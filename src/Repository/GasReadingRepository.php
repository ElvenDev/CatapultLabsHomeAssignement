<?php

namespace App\Repository;

use App\Entity\GasReading;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GasReading|null find($id, $lockMode = null, $lockVersion = null)
 * @method GasReading|null findOneBy(array $criteria, array $orderBy = null)
 * @method GasReading[]    findAll()
 * @method GasReading[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GasReadingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GasReading::class);
    }

    /**
     * @param int $companyId
     * @param \DateTime $dateAfter
     * @return GasReading[] Returns an array of GasReading objects
     */
    public function findByDateAfterAndCompanyId($companyId, $dateAfter)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.created > :date_after')
            ->setParameter('date_after', $dateAfter)
            ->andWhere('g.company = :company_id')
            ->setParameter('company_id', $companyId)
            ->orderBy('g.created', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param \DateTime $beginningDateTime
     * @param \DateTime  $endDateTime
     * @return GasReading[] Returns an array of GasReading objects
     */
    public function findAllGasReadingsBetweenDateTimes($beginningDateTime, $endDateTime)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.created > :beginning')
            ->setParameter('beginning', $beginningDateTime)
            ->andWhere('g.created < :end')
            ->setParameter('end', $endDateTime)
            ->getQuery()
            ->getResult()
        ;
    }
}
