<?php

namespace App\Entity;

use App\Repository\MenuPersonnaliseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Menu composé librement par l'utilisateur.
 * Stocké par sessionId pour l'instant, sera lié à un User plus tard.
 */
#[ORM\Entity(repositoryClass: MenuPersonnaliseRepository::class)]
class MenuPersonnalise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 128)]
    private ?string $sessionId = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, MenuPersonnaliseLigne>
     */
    #[ORM\OneToMany(targetEntity: MenuPersonnaliseLigne::class, mappedBy: 'menuPersonnalise', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignes;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): static { $this->nom = $nom; return $this; }

    public function getSessionId(): ?string { return $this->sessionId; }
    public function setSessionId(string $sessionId): static { $this->sessionId = $sessionId; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /** @return Collection<int, MenuPersonnaliseLigne> */
    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(MenuPersonnaliseLigne $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setMenuPersonnalise($this);
        }
        return $this;
    }

    public function removeLigne(MenuPersonnaliseLigne $ligne): static
    {
        if ($this->lignes->removeElement($ligne)) {
            if ($ligne->getMenuPersonnalise() === $this) {
                $ligne->setMenuPersonnalise(null);
            }
        }
        return $this;
    }

    public function getPrixTotal(): float
    {
        $total = 0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne->getPrixLigne();
        }
        return $total;
    }
}
