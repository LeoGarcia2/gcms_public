<?php

namespace App\Repository;

use App\Entity\CTAaa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CTAaa|null find($id, $lockMode = null, $lockVersion = null)
 * @method CTAaa|null findOneBy(array $criteria, array $orderBy = null)
 * @method CTAaa[]    findAll()
 * @method CTAaa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CTAaaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CTAaa::class);
    }

    // /**
    //  * @return CTAaa[] Returns an array of CTAaa objects
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
    public function findOneBySomeField($value): ?CTAaa
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
