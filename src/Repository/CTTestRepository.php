<?php

namespace App\Repository;

use App\Entity\CTTest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CTTest|null find($id, $lockMode = null, $lockVersion = null)
 * @method CTTest|null findOneBy(array $criteria, array $orderBy = null)
 * @method CTTest[]    findAll()
 * @method CTTest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CTTestRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CTTest::class);
    }

    // /**
    //  * @return CTTest[] Returns an array of CTTest objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CTTest
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
