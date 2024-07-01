<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Efrogg\Synergy\Entity\SynergyNumericIdEntityTrait;
use Efrogg\Synergy\Mapping\SynergyEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[SynergyEntity]
class Category extends AbstractSynergyBudgetEntity
{
    use SynergyNumericIdEntityTrait;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Budget $budget = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?Envelope $envelope = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'category')]
    private Collection $movements;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
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

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function setBudget(?Budget $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getEnvelope(): ?Envelope
    {
        return $this->envelope;
    }

    public function setEnvelope(?Envelope $envelope): static
    {
        $this->envelope = $envelope;

        return $this;
    }

    /**
     * @return Collection<int, Movement>
     */
    public function getMovements(): Collection
    {
        return $this->movements;
    }

    public function addMovement(Movement $movement): static
    {
        if (!$this->movements->contains($movement)) {
            $this->movements->add($movement);
            $movement->setCategory($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getCategory() === $this) {
                $movement->setCategory(null);
            }
        }

        return $this;
    }
}
