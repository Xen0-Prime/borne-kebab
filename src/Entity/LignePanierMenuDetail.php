<?php

namespace App\Entity;

use App\Repository\LignePanierMenuDetailRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Détail d'un composant dans une ligne de menu :
 * quel produit a été choisi et quelles options ont été sélectionnées.
 */
#[ORM\Entity(repositoryClass: LignePanierMenuDetailRepository::class)]
class LignePanierMenuDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: LignePanierMenu::class, inversedBy: 'details')]
    #[ORM\JoinColumn(nullable: false)]
    private ?LignePanierMenu $lignePanierMenu = null;

    #[ORM\ManyToOne(targetEntity: MenuComposant::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?MenuComposant $composant = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    /**
     * Options choisies pour ce produit dans le menu
     * @var Collection<int, Option>
     */
    #[ORM\ManyToMany(targetEntity: Option::class)]
    #[ORM\JoinTable(name: 'ligne_panier_menu_detail_option')]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getLignePanierMenu(): ?LignePanierMenu { return $this->lignePanierMenu; }
    public function setLignePanierMenu(?LignePanierMenu $lignePanierMenu): static { $this->lignePanierMenu = $lignePanierMenu; return $this; }

    public function getComposant(): ?MenuComposant { return $this->composant; }
    public function setComposant(?MenuComposant $composant): static { $this->composant = $composant; return $this; }

    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): static { $this->produit = $produit; return $this; }

    /** @return Collection<int, Option> */
    public function getOptions(): Collection { return $this->options; }

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
}
