<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    // CORRECTION : valeur par défaut 0.00 (option gratuite par défaut)
    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, options: ['default' => '0.00'])]
    private ?string $prixSupplementaire = null;

    // type : 'ajout' ou 'retrait'
    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(options: ['default' => true])]
    private ?bool $disponible = null;

    // AJOUT : côté inverse de la relation ManyToMany avec Produit
    // mappedBy: 'options' correspond à la propriété $options dans Produit
    // C'est le côté "inverse" : Doctrine ne crée PAS de table de jointure ici
    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, mappedBy: 'options')]
    private Collection $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrixSupplementaire(): ?string
    {
        return $this->prixSupplementaire;
    }

    public function setPrixSupplementaire(string $prixSupplementaire): static
    {
        $this->prixSupplementaire = $prixSupplementaire;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function isDisponible(): ?bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;
        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->addOption($this);
        }
        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            $produit->removeOption($this);
        }
        return $this;
    }
}
