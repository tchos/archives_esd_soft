<?php

namespace App\Entity\App\Entity\Main;

use App\Repository\Main\EsdManuelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EsdManuelRepository::class)]
class EsdManuel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 7)]
    private ?string $matricule = null;

    #[ORM\Column(length: 64)]
    private ?string $numero = null;

    #[ORM\Column]
    private ?int $montant = null;

    #[ORM\Column(length: 128)]
    private ?string $signataire = null;

    #[ORM\Column(length: 128)]
    private ?string $service = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_signature = null;

    #[ORM\Column(length: 64)]
    private ?string $copie_scannee = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

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

    public function getSignataire(): ?string
    {
        return $this->signataire;
    }

    public function setSignataire(string $signataire): static
    {
        $this->signataire = $signataire;

        return $this;
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->date_signature;
    }

    public function setDateSignature(\DateTimeInterface $date_signature): static
    {
        $this->date_signature = $date_signature;

        return $this;
    }

    public function getCopieScannee(): ?string
    {
        return $this->copie_scannee;
    }

    public function setCopieScannee(string $copie_scannee): static
    {
        $this->copie_scannee = $copie_scannee;

        return $this;
    }
}
