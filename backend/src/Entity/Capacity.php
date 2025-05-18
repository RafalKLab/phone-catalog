<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource]
#[ORM\Entity]
#[ORM\Table(name: 'capacities')]
class Capacity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups(['item:read'])]
    private string $size;

    #[ORM\OneToMany(mappedBy: 'capacity', targetEntity: Item::class)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return $this->size;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }
}
