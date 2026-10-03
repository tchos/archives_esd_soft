<?php

namespace App\Entity\Main;

use App\Repository\Main\EsdManuelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EsdManuelRepository::class)]
#[UniqueEntity(
    fields: ['matricule', 'numero', 'date_signature'],
    message: 'Un ESD Manuel existe déjà avec ce matricule, ce numéro et cette date.'
)]
class EsdManuel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 7)]
    #[Assert\Regex(
        pattern:"/(^[A-Z][0-9]{6}$)|(^[0-9]{5,6}[A-Z]$)/",
        message:"Le matricule {{ value }} n'est pas un matricule valide."
    )]
    private ?string $matricule = null;

    #[ORM\Column(length: 64)]
    private ?string $numero = null;

    #[ORM\Column]
    #[Assert\Assert\GreaterThanOrEqual(
        value: 10000,
        message: "Le montant minimum d'ESD doit être {{ compared_value }} FCFA."
    )]
    private ?int $montant = null;

    #[ORM\Column(length: 128)]
    private ?string $service = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThan(
        'today',
        message: "La date de signature doit être antérieure à celle d'aujourd'hui."
    )]
    private ?\DateTimeInterface $date_signature = null;

    #[ORM\Column(length: 64)]
    #[Assert\Length(
        min: 4,
        max: 64,
        minMessage: "Le nom du signataire doit avoir au minimum {{ limit }} caractères",
        maxMessage: "Le nom du signataire doit avoir au maximum {{ limit }} caractères"
    )]
    #[Assert\Regex(
        pattern: "/^([A-Z])+$/",
        message: "Le nom du signataire doit être en majuscule"
    )]
    private ?string $signataire = null;

    #[ORM\Column(length: 64)]
    private ?string $copie_scannee = null;

    #[ORM\Column(length: 16)]
    private ?string $type = null;

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

    public function getSignataire(): ?string
    {
        return $this->signataire;
    }

    public function setSignataire(string $signataire): static
    {
        $this->signataire = $signataire;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
