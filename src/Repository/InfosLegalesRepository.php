<?php

namespace App\Repository;

use App\Entity\InfosLegales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InfosLegales|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfosLegales|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfosLegales[]    findAll()
 * @method InfosLegales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfosLegalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfosLegales::class);
    }

    // /**
    //  * @return InfosLegales[] Returns an array of InfosLegales objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InfosLegales
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
