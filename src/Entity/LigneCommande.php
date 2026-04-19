<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite = null;

    // Prix au moment de la commande (snapshot) — ne change jamais même si le produit est modifié
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $prixUnitaire = null;

    // Nom du produit au moment de la commande (snapshot)
    // Permet d'afficher le nom même si le produit est supprimé plus tard
    #[ORM\Column(length: 150)]
    private ?string $nomProduit = null;

    // Tableau JSON des options choisies (snapshot complet : nom + prix)
    // Exemple : [{"nom": "Fromage fondu", "prix": "1.00"}, {"nom": "Sans oignons", "prix": "0.00"}]
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $optionsChoisies = null;

    // CORRECTION : ajout de inversedBy: 'lignes' pour lier à Commande::$lignes
    // nullable: false car une ligne appartient toujours à une commande
    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    // CORRECTION : nullable: true car le produit peut être supprimé
    // (on garde quand même la ligne avec nomProduit en snapshot)
    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Produit $produit = null;

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

    public function getNomProduit(): ?string
    {
        return $this->nomProduit;
    }

    public function setNomProduit(string $nomProduit): static
    {
        $this->nomProduit = $nomProduit;
        return $this;
    }

    public function getOptionsChoisies(): ?array
    {
        return $this->optionsChoisies;
    }

    public function setOptionsChoisies(?array $optionsChoisies): static
    {
        $this->optionsChoisies = $optionsChoisies;
        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
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

    // BONUS : calcul du sous-total de cette ligne
    public function getSousTotal(): float
    {
        return (float) $this->prixUnitaire * $this->quantite;
    }
}
