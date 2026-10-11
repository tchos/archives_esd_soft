<?php

namespace App\Repository\Main;

use App\Entity\Main\Esd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Esd>
 */
class EsdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Esd::class);
    }

    public function findOneByNumesdAndMatricule(string $numesd, string $matricule): ?Esd
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.numesd = :numesd')
            ->andWhere('e.matricule = :matricule')
            ->setParameter('numesd' , $numesd)
            ->setParameter('matricule', $matricule)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Esd[] Returns an array of Esd objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Esd
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
