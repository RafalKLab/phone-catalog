<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    private string $size;

    #[ORM\ManyToMany(targetEntity: Model::class, mappedBy: 'capacities')]
    private Collection $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
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

    public function getModels(): Collection
    {
        return $this->models;
    }
}
