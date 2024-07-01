<?php

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Efrogg\Synergy\Entity\SynergyNumericIdEntityTrait;
use Efrogg\Synergy\Mapping\SynergyEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
#[SynergyEntity]
class Import extends AbstractSynergyBudgetEntity
{
    use TimestampableEntity;
    use SynergyNumericIdEntityTrait;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'import')]
    private Collection $movements;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Budget $budget = null;

    public function __construct()
    {
        $this->movements = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

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
            $movement->setImport($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getImport() === $this) {
                $movement->setImport(null);
            }
        }

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
}
