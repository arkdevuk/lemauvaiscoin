<?php

namespace App\Entity;

use App\Repository\AnnonceAttributeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceAttributeRepository::class)]
class AnnonceAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'attributes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Annonce $parent = null;

    public function __construct(
        Annonce $parent,
        string $name,
        string $value,
    ) {
        $this->value = $value;
        $this->name = $name;
        $this->parent = $parent;
        $this->parent->addAttribute($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }


    public function getParent(): ?Annonce
    {
        return $this->parent;
    }
}
