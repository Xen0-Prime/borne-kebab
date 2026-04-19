<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // CORRECTION : unique: true (un sessionId ne doit appartenir qu'à un seul panier)
    #[ORM\Column(length: 128, unique: true)]
    private ?string $sessionId = null;

    // CORRECTION 1 : faute de frappe "createedAt" → "createdAt"
    // CORRECTION 2 : DateTimeImmutable recommandé en Symfony (immuable = plus sûr)
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // CORRECTION : DateTimeImmutable au lieu de DateTime
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    // AJOUT : relation OneToMany vers LignePanier
    // mappedBy: 'panier' correspond à la propriété $panier dans LignePanier
    // cascade persist+remove : les lignes sont sauvegardées/supprimées avec le panier
    // orphanRemoval : si une ligne est retirée de la collection, elle est supprimée en BDD
    /**
     * @var Collection<int, LignePanier>
     */
    #[ORM\OneToMany(targetEntity: LignePanier::class, mappedBy: 'panier', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): static
    {
        $this->sessionId = $sessionId;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, LignePanier>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(LignePanier $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setPanier($this);
        }
        return $this;
    }

    public function removeLigne(LignePanier $ligne): static
    {
        if ($this->lignes->removeElement($ligne)) {
            if ($ligne->getPanier() === $this) {
                $ligne->setPanier(null);
            }
        }
        return $this;
    }

    // BONUS : méthode utilitaire pour calculer le total du panier
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->lignes as $ligne) {
            $total += (float) $ligne->getPrixUnitaire() * $ligne->getQuantite();
        }
        return $total;
    }
}
