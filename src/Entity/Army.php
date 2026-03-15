<?php

namespace App\Entity;

use App\Repository\ArmyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArmyRepository::class)]
class Army
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $logo = null;

    #[ORM\Column(length: 10)]
    private ?string $shortname = null;

    /**
     * @var Collection<int, ArmyList>
     */
    #[ORM\OneToMany(targetEntity: ArmyList::class, mappedBy: 'army', orphanRemoval: true)]
    private Collection $armyLists;

    public function __construct()
    {
        $this->armyLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): static
    {
        $this->shortname = $shortname;

        return $this;
    }

    /**
     * @return Collection<int, ArmyList>
     */
    public function getArmyLists(): Collection
    {
        return $this->armyLists;
    }

    public function addArmyList(ArmyList $armyList): static
    {
        if (!$this->armyLists->contains($armyList)) {
            $this->armyLists->add($armyList);
            $armyList->setArmy($this);
        }

        return $this;
    }

    public function removeArmyList(ArmyList $armyList): static
    {
        if ($this->armyLists->removeElement($armyList)) {
            // set the owning side to null (unless already changed)
            if ($armyList->getArmy() === $this) {
                $armyList->setArmy(null);
            }
        }

        return $this;
    }
}
