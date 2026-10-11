<?php

namespace App\Entity;

use App\Repository\AiglesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AiglesRepository::class)]
class Aigles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 8)]
    private ?string $rubrique = null;

    #[ORM\Column(length: 3)]
    private ?string $codeAnt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRubrique(): ?string
    {
        return $this->rubrique;
    }

    public function setRubrique(string $rubrique): static
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    public function getCodeAnt(): ?string
    {
        return $this->codeAnt;
    }

    public function setCodeAnt(string $codeAnt): static
    {
        $this->codeAnt = $codeAnt;

        return $this;
    }
}
