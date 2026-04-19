<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // CORRECTION : unique: true (deux commandes ne peuvent pas avoir le même numéro)
    #[ORM\Column(length: 20, unique: true)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    private ?string $total = null;

    // CORRECTION : valeur par défaut 'en_attente'
    // Valeurs possibles : en_attente | en_preparation | prete | livree
    #[ORM\Column(length: 30, options: ['default' => 'en_attente'])]
    private ?string $statut = null;

    // CORRECTION : DateTimeImmutable recommandé en Symfony
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // CORRECTION : type TEXT pour un commentaire potentiellement long
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    // AJOUT : relation OneToMany vers LigneCommande
    // mappedBy: 'commande' correspond à la propriété $commande dans LigneCommande
    // cascade persist+remove : les lignes sont enregistrées/supprimées avec la commande
    // orphanRemoval : si une ligne est retirée de la collection, elle est supprimée en BDD
    /**
     * @var Collection<int, LigneCommande>
     */
    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'commande', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->statut = 'en_attente';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(LigneCommande $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setCommande($this);
        }
        return $this;
    }

    public function removeLigne(LigneCommande $ligne): static
    {
        if ($this->lignes->removeElement($ligne)) {
            if ($ligne->getCommande() === $this) {
                $ligne->setCommande(null);
            }
        }
        return $this;
    }
}
