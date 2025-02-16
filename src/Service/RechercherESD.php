<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class RechercherESD
{
    private $manager;
    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }
    /**
     * Permet de rechercher un ESD à partir d'un matricule ou d'un numéro d'ESD
     *
     * @param [string] $infos
     *
     * @return Entity Esd
     */
    public function findESD($infos)
    {
        $mots_cles = explode(' ', $infos);
        for ($i = 0; $i < sizeof($mots_cles); ++$i) {
            if ($i == 0) {
                $recherche = '
                    SELECT e
                    FROM App\Entity\Main\Esd e
                    WHERE (e.matricule LIKE :mot_clef'.$i.' OR e.numesd LIKE :mot_clef'.$i.')
                ';
            } else {
                $recherche .= ' AND (e.matricule LIKE :mot_clef'.$i.'
                    OR e.numesd LIKE :mot_clef'.$i.')';
            }
        }

        $query = $this->manager->createQuery($recherche);
        for ($i = 0; $i < sizeof($mots_cles); ++$i) {
            $mot_clef = trim($mots_cles[$i]);
            $query->setParameter('mot_clef'.$i.'', '%'.$mot_clef.'%');
        }

        return $query->getResult();
    }
}