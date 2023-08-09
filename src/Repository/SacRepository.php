<?php

namespace App\Repository;

use App\Entity\Sac;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sac>
 *
 * @method Sac|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sac|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sac[]    findAll()
 * @method Sac[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SacRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sac::class);
    }

//    /**
//     * @return Sac[] Returns an array of Sac objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sac
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
