<?php

namespace App\Entity;

use App\Repository\LignePanierMenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Une ligne de menu dans le panier.
 * Regroupe un menu choisi, sa quantité et le détail des produits/options sélectionnés.
 */
#[ORM\Entity(repositoryClass: LignePanierMenuRepository::class)]
class LignePanierMenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(options: ['default' => 1])]
    private ?int $quantite = 1;

    /** Prix total d'un menu (base + suppléments des options) au moment de l'ajout */
    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $prixUnitaire = null;

    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'lignesMenu')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Menu $menu = null;

    /**
     * Détail de chaque composant sélectionné dans ce menu
     * @var Collection<int, LignePanierMenuDetail>
     */
    #[ORM\OneToMany(targetEntity: LignePanierMenuDetail::class, mappedBy: 'lignePanierMenu', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $details;

    public function __construct()
    {
        $this->details = new ArrayCollection();
        $this->quantite = 1;
    }

    public function getId(): ?int { return $this->id; }

    public function getQuantite(): ?int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; return $this; }

    public function getPrixUnitaire(): ?string { return $this->prixUnitaire; }
    public function setPrixUnitaire(string $prixUnitaire): static { $this->prixUnitaire = $prixUnitaire; return $this; }

    public function getPanier(): ?Panier { return $this->panier; }
    public function setPanier(?Panier $panier): static { $this->panier = $panier; return $this; }

    public function getMenu(): ?Menu { return $this->menu; }
    public function setMenu(?Menu $menu): static { $this->menu = $menu; return $this; }

    /** @return Collection<int, LignePanierMenuDetail> */
    public function getDetails(): Collection { return $this->details; }

    public function addDetail(LignePanierMenuDetail $detail): static
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
            $detail->setLignePanierMenu($this);
        }
        return $this;
    }

    public function removeDetail(LignePanierMenuDetail $detail): static
    {
        if ($this->details->removeElement($detail)) {
            if ($detail->getLignePanierMenu() === $this) {
                $detail->setLignePanierMenu(null);
            }
        }
        return $this;
    }

    public function getSousTotal(): float
    {
        return (float) $this->prixUnitaire * $this->quantite;
    }
}
