<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

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
    #[Groups(['item:read'])]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'models')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item:read'])]
    private Brand $brand;

    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Item::class)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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
}
