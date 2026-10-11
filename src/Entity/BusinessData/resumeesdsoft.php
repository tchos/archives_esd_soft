<?php

namespace App\Entity\BusinessData;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\BusinessData\resumeesdsoftRepository", readOnly: true)]
#[ORM\Table(name: "public.resumeesdsoft")]
#[ORM\Immutable] // Rend l'entité en lecture seule

class resumeesdsoft
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 255)]
    private ?string $matricule = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $numesd = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $ministereagent = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $coderenumeration = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $libellerenumeration = null;

    #[ORM\Column(type: "float")]
    private ?float $montant = null;

    #[ORM\Column(type: "string", length: 6)]
    private ?string $codepaiementesd = null;

    #[ORM\Column(type: "float")]
    private ?float $montantesd = null;

    #[ORM\Column(type: "string", length: 10)]
    private ?string $datecreationesd = null;

    #[ORM\Column(type: "bigint")]
    private ?int $creatorid = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $creatorfullname = null;

    #[ORM\Column(type: "string", length: 10)]
    private ?string $datepaiementesd = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $statut = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $GRADE = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $signataire = null;

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function getNumesd(): ?string
    {
        return $this->numesd;
    }

    public function getMinistereagent(): ?string
    {
        return $this->ministereagent;
    }

    public function getCoderenumeration(): ?string
    {
        return $this->coderenumeration;
    }

    public function getLibellerenumeration(): ?string
    {
        return $this->libellerenumeration;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function getCodepaiementesd(): ?string
    {
        return $this->codepaiementesd;
    }

    public function getMontantesd(): ?float
    {
        return $this->montantesd;
    }

    public function getDatecreationesd(): ?string
    {
        return $this->datecreationesd;
    }

    public function getCreatorid(): ?int
    {
        return $this->creatorid;
    }

    public function getCreatorfullname(): ?string
    {
        return $this->creatorfullname;
    }

    public function getDatepaiementesd(): ?string
    {
        return $this->datepaiementesd;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function getGRADE(): ?string
    {
        return $this->GRADE;
    }

    public function getSignataire(): ?string
    {
        return $this->signataire;
    }
}