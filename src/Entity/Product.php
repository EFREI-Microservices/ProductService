<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column]
    private int $price;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $available;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    final public function getId(): ?int
    {
        return $this->id;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    final public function getDescription(): string
    {
        return $this->description;
    }

    final public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    final public function getPrice(): int
    {
        return $this->price;
    }

    final public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    final public function getAvailable(): bool
    {
        return $this->available;
    }

    final public function setAvailable(bool $available): static
    {
        $this->available = $available;

        return $this;
    }

    final public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    final public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
