<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[UniqueEntity('name')]
#[UniqueEntity('tax')]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(length: 2)]
    #[Assert\Country]
    #[Assert\NotBlank]
    private ?string $tax = null;

    #[ORM\Column]
    #[Assert\Positive]
    #[Assert\LessThan(100)]
    private ?int $tax_value = null;

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

    public function getTax(): ?string
    {
        return $this->tax;
    }

    public function setTax(string $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getTaxValue(): ?int
    {
        return $this->tax_value;
    }

    public function setTaxValue(int $tax_value): self
    {
        $this->tax_value = $tax_value;

        return $this;
    }
}
