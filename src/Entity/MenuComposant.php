<?php

namespace App\Entity;

use App\Repository\MenuComposantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Un composant d'un menu (ex : "Kebab", "Boisson", "Accompagnement").
 * Le client choisira un produit parmi ceux de la catégorie associée.
 */
#[ORM\Entity(repositoryClass: MenuComposantRepository::class)]
class MenuComposant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $position = 0;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'composants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    /**
     * Catégorie source des produits proposés pour ce composant.
     * Ex: composant "Kebab" → catégorie "Kebabs"
     */
    #[ORM\ManyToOne(targetEntity: Categorie::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $categorie = null;

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPosition(): ?int { return $this->position; }
    public function setPosition(int $position): static { $this->position = $position; return $this; }

    public function getMenu(): ?Menu { return $this->menu; }
    public function setMenu(?Menu $menu): static { $this->menu = $menu; return $this; }

    public function getCategorie(): ?Categorie { return $this->categorie; }
    public function setCategorie(?Categorie $categorie): static { $this->categorie = $categorie; return $this; }
}
