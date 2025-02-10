<?php

namespace App\Entity\Main;

use App\Repository\Main\EsdRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EsdRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_numesd_matricule', columns: ['numesd', 'matricule'])]
#[ORM\HasLifecycleCallbacks()]
#[UniqueEntity(fields: ['numesd','matricule'], message: 'Cet ESD a deja ete archive pour ce matricule !!!')]
class Esd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $numesd = null;

    #[ORM\Column(length: 8)]
    private ?string $matricule = null;

    #[ORM\Column(length: 255)]
    private ?string $nomagent = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $codepaiement = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateesd = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier_electronique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier_scanne = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_archivage_auto = null;

    #[ORM\Column]
    private ?bool $isDeleted = null;

    /**
     * CallBack appelé à chaque fois que l'on veut enregistrer un user pour
     * prendre automatiquement sa date de création du compte .
     */
    #[ORM\PrePersist]
    public function PrePersist()
    {
        if (empty($this->date_archivage_auto)) {
            $this->date_archivage_auto = new \DateTimeImmutable();
        }

        $this->date_archivage_auto = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumesd(): ?string
    {
        return $this->numesd;
    }

    public function setNumesd(string $numesd): static
    {
        $this->numesd = $numesd;

        return $this;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): static
    {
        $this->matricule = $matricule;

        return $this;
    }

    public function getCodepaiement(): ?string
    {
        return $this->codepaiement;
    }

    public function setCodepaiement(string $codepaiement): static
    {
        $this->codepaiement = $codepaiement;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateesd(): ?\DateTimeInterface
    {
        return $this->dateesd;
    }

    public function setDateesd(?\DateTimeInterface $dateesd): static
    {
        $this->dateesd = $dateesd;

        return $this;
    }

    public function getNomagent(): ?string
    {
        return $this->nomagent;
    }

    public function setNomagent(string $nomagent): static
    {
        $this->nomagent = $nomagent;

        return $this;
    }

    public function getFichierElectronique(): ?string
    {
        return $this->fichier_electronique;
    }

    public function setFichierElectronique(?string $fichier_electronique): static
    {
        $this->fichier_electronique = $fichier_electronique;

        return $this;
    }

    public function getFichierScanne(): ?string
    {
        return $this->fichier_scanne;
    }

    public function setFichierScanne(?string $fichier_scanne): static
    {
        $this->fichier_scanne = $fichier_scanne;
        return $this;
    }

    public function getDateArchivageAuto(): ?\DateTimeInterface
    {
        return $this->date_archivage_auto;
    }

    public function setDateArchivageAuto(\DateTimeInterface $date_archivage_auto): static
    {
        $this->date_archivage_auto = $date_archivage_auto;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }
}
