<?php

namespace App\Repository;

use App\Entity\CTAzaza;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CTAzaza|null find($id, $lockMode = null, $lockVersion = null)
 * @method CTAzaza|null findOneBy(array $criteria, array $orderBy = null)
 * @method CTAzaza[]    findAll()
 * @method CTAzaza[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CTAzazaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CTAzaza::class);
    }

    // /**
    //  * @return CTAzaza[] Returns an array of CTAzaza objects
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
    public function findOneBySomeField($value): ?CTAzaza
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
