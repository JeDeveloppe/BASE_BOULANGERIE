<?php

namespace App\Repository;

use App\Entity\ReservationDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReservationDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationDetails[]    findAll()
 * @method ReservationDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationDetails::class);
    }


    public function findCountGroupByProduit($reservation): array
    {
        $qb = $this->createQueryBuilder('r');
        return $qb
        ->select('COUNT(r.produit)')
        ->where($qb->expr()->in('r.id', ':reservation'))
        ->setParameter('reservation', $reservation)
        ->getQuery()
        ->getResult()

    ;
    }

    // /**
    //  * @return ReservationDetails[] Returns an array of ReservationDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReservationDetails
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
