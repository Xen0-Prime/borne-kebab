<?php

namespace App\Entity;

use App\Repository\LignePanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LignePanierRepository::class)]
class LignePanier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => 1])]
    private ?int $quantite = null;

    // Prix unitaire au moment de l'ajout au panier (produit de base + options)
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $prixUnitaire = null;

    // CORRECTION : ajout de inversedBy: 'lignes' pour lier à Panier::$lignes
    // Sans inversedBy, Doctrine ne sait pas que c'est la même relation des deux côtés
    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    // Pas de inversedBy ici car Produit n'a pas de collection de LignePanier
    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    // Options sélectionnées par l'utilisateur pour cette ligne
    // Pas de inversedBy : relation unidirectionnelle (Option n'a pas besoin de connaître les LignePanier)
    // Doctrine crée une table de jointure ligne_panier_option automatiquement
    /**
     * @var Collection<int, Option>
     */
    #[ORM\ManyToMany(targetEntity: Option::class)]
    #[ORM\JoinTable(name: 'ligne_panier_option')]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
        $this->quantite = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getPrixUnitaire(): ?string
    {
        return $this->prixUnitaire;
    }

    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): static
    {
        $this->panier = $panier;
        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;
        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): static
    {
        if (!$this->options->contains($option)) {
            $this->options->add($option);
        }
        return $this;
    }

    public function removeOption(Option $option): static
    {
        $this->options->removeElement($option);
        return $this;
    }

    // BONUS : calcul du sous-total de cette ligne
    public function getSousTotal(): float
    {
        return (float) $this->prixUnitaire * $this->quantite;
    }
}
