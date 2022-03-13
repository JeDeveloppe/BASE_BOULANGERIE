<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Document;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function ventesParMois($startDate,$endDate)
    {
   
        return $this->createQueryBuilder('d')
                        ->select('COALESCE(SUM(d.totalHT),0)')
                        ->where('d.createdAt BETWEEN :start AND :end')
                        ->setParameter('start', $startDate)
                        ->setParameter('end', $endDate)
                        ->getQuery()
                        ->getSingleScalarResult()
                        ;
    }

      
    public function rechercheDocumentParNumeroCommande($numCommande)
    {
        return $this->createQueryBuilder('d')
                ->where('d.numeroFacture LIKE :numero')
                ->setParameter('numero','%'.$numCommande.'%')
                ->getQuery()
                ->getResult()
        ;
    }



    // /**
    //  * @return Document[] Returns an array of Document objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Document
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
