<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    paginationEnabled: true,
    paginationItemsPerPage: 10,
    paginationMaximumItemsPerPage: 100,
    paginationClientItemsPerPage: true,
    paginationClientEnabled: true,
    normalizationContext: ['groups' => ['item:read']]
)]
#[ORM\Entity]
#[ORM\Table(name: 'items')]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['item:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Groups(['item:read'])]
    private int $price; // stored in cents

    #[ORM\Column(type: 'string', length: 5)]
    #[Groups(['item:read'])]
    private string $grade;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item:read'])]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item:read'])]
    private Model $model;

    #[ORM\ManyToOne(targetEntity: Capacity::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['item:read'])]
    private Capacity $capacity;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $itemHash;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getGrade(): string
    {
        return $this->grade;
    }

    public function getCapacity(): Capacity
    {
        return $this->capacity;
    }
}
