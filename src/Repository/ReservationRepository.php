<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }


    public function verifReservationNonPayeeExiste($user,$start,$end)
    {
        return $this->createQueryBuilder('r')
            ->where('r.user = :user')
            ->andWhere('r.statutPaiement = :statutPaiement')
            ->andWhere('r.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('statutPaiement', 'EN_ATTENTE_DE_PAIEMENT')
            ->setParameter('end', $end)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOldReservationsNonEmportees($dateMinuit)
    {
        return $this->createQueryBuilder('r')
            ->where('r.statutReservation = :statutReservation')
            ->andWhere('r.createdAt <= :date')
            ->orderBy('r.createdAt','ASC')
            ->setParameter('statutReservation', 'PAS_EMPORTEE')
            ->setParameter('date', $dateMinuit)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findReservations($start,$end)
    {
        return $this->createQueryBuilder('r')
            ->where('r.createdAt BETWEEN :start AND :end')
            ->andWhere('r.statutPaiement = :statutPaiement')
            ->orderBy('r.createdAt','ASC')
            ->setParameter('statutPaiement', 'FACTURE_OK')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Reservation
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
