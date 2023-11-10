<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use App\Traits\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Produits
{

    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToOne(inversedBy: 'produits', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?References $reference = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Categories $categorie = null;

    #[ORM\ManyToMany(targetEntity: Distributeurs::class, inversedBy: 'produits')]
    private Collection $distributeur;

    #[ORM\OneToMany(mappedBy: 'produits', targetEntity: Photos::class, cascade:['persist'])]
    private ?Collection $photos = null;

   

    public function __construct()
    {
        $this->distributeur = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

 

    public function getReference(): ?References
    {
        return $this->reference;
    }

    public function setReference(References $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCategorie(): ?Categories
    {
        return $this->categorie;
    }

    public function setCategorie(?Categories $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection<int, Distributeurs>
     */
    public function getDistributeur(): Collection
    {
        return $this->distributeur;
    }

    public function addDistributeur(Distributeurs $distributeur): static
    {
        if (!$this->distributeur->contains($distributeur)) {
            $this->distributeur->add($distributeur);
        }

        return $this;
    }

    public function removeDistributeur(Distributeurs $distributeur): static
    {
        $this->distributeur->removeElement($distributeur);

        return $this;
    }

    /**
     * @return Collection<int, Photos>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photos $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setProduits($this);
        }

        return $this;
    }

    public function removePhoto(Photos $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getProduits() === $this) {
                $photo->setProduits(null);
            }
        }

        return $this;
    }
}
