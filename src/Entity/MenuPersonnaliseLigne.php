<?php

namespace App\Entity;

use App\Repository\MenuPersonnaliseLigneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Un produit (avec options) dans un menu personnalisé.
 */
#[ORM\Entity(repositoryClass: MenuPersonnaliseLigneRepository::class)]
class MenuPersonnaliseLigne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: MenuPersonnalise::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MenuPersonnalise $menuPersonnalise = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    /**
     * @var Collection<int, Option>
     */
    #[ORM\ManyToMany(targetEntity: Option::class)]
    #[ORM\JoinTable(name: 'menu_perso_ligne_option')]
    private Collection $options;

    public function __construct()
    {
        $this->options = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getMenuPersonnalise(): ?MenuPersonnalise { return $this->menuPersonnalise; }
    public function setMenuPersonnalise(?MenuPersonnalise $menuPersonnalise): static { $this->menuPersonnalise = $menuPersonnalise; return $this; }

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

    public function getPrixLigne(): float
    {
        $prix = (float) $this->produit->getPrix();
        foreach ($this->options as $option) {
            $prix += (float) $option->getPrixSupplementaire();
        }
        return $prix;
    }
}
