<?php

namespace App\Repository\Main;

use App\Entity\Main\EsdManuel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EsdManuel>
 */
class EsdManuelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EsdManuel::class);
    }

    /**
     * Recherche d'un ESD par matricule et/ou numéro.
     *
     * @return EsdManuel[]
     */
    public function search(?string $matricule = null, ?string $numero = null): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($matricule !== null && $matricule !== '') {
            $qb->andWhere('e.matricule LIKE :matricule')
                ->setParameter('matricule', '%' . $matricule . '%');
        }

        if ($numero !== null && $numero !== '') {
            $qb->andWhere('e.numero LIKE :numero')
                ->setParameter('numero', '%' . $numero . '%');
        }

        // Si aucun critère n'est fourni, on renvoie un tableau vide
        if ($matricule === null || $matricule === '') {
            if ($numero === null || $numero === '') {
                return [];
            }
        }

        return $qb->orderBy('e.date_signature', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return EsdManuel[] Returns an array of EsdManuel objects
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

    //    public function findOneBySomeField($value): ?EsdManuel
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
