<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ApiResource]
#[ORM\Entity]
#[ORM\Table(name: 'models')]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'models')]
    #[ORM\JoinColumn(nullable: false)]
    private Brand $brand;

    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Item::class)]
    private Collection $items;

    #[ORM\ManyToMany(targetEntity: Capacity::class, inversedBy: 'models')]
    #[ORM\JoinTable(name: 'model_capacity')]
    private Collection $capacities;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->capacities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function getBrand(): Brand
    {
        return $this->brand;
    }

    public function getCapacities(): Collection
    {
        return $this->capacities;
    }
}
