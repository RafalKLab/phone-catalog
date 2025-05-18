<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[ORM\Entity]
#[ORM\Table(name: 'brands')]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['item:read'])]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Model::class)]
    private Collection $models;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModels(): Collection
    {
        return $this->models;
    }
}
