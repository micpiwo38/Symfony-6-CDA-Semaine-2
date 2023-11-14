<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;

use App\Validator\ProductCodeCompatibilityValidator;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
//Attribut PHP 8 + appel de la methode CallBack(ARRAY Nom de la classe de validation + nom de la methode)
#[Assert\Callback([ProductCodeCompatibilityValidator::class, 'validate'])]
class Product
{
   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[A-Z]{3}[0-9]{4}$/')]//Correspond à un mot d’au moins 3 lettres (majuscule) et  4 chiffres
    private ?string $productCode = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $category = null;

    public function getRoot(string $property, int $x){
        return substr($this->$property, 0, 3);
    }

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(groups:['prixProduit'])]
    #[Assert\Positive(groups:['prixProduit'])]
    private ?float $price = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): static
    {
        $this->productCode = $productCode;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }
}
