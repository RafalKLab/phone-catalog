<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource]
#[ORM\Entity]
#[ORM\Table(name: 'items')]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $price; // stored in cents

    #[ORM\Column(type: 'string', length: 5)]
    private string $grade;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private Model $model;

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
}
