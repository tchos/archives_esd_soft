<?php

namespace App\Repository\BusinessData;

use App\Entity\BusinessData\resumeesdsoft;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class resumeesdsoftRepository extends ServiceEntityRepository
{
    private $em;
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, resumeesdsoft::class);
        $this->em = $registry->getManager('business_data');
    }

    /**
     * Trouver le code de paiement et le montant d'un ESD par matricule et numesd
     */
    public function findByMatriculeAndNumesd(string $matricule, string $numesd): array
    {
       $query = $this->em->createQuery(
            'SELECT DISTINCT r.codepaiementesd as code, r.montantesd as montant
            FROM App\Entity\BusinessData\resumeesdsoft r
            WHERE r.matricule = :matricule and r.numesd = :numesd')
           ->setParameter('matricule', $matricule)
           ->setParameter('numesd', $numesd);

        // returns an array of Product objects
        return $query->getResult();

    }
}