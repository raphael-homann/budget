<?php

namespace App\Entity;

use App\Repository\EnvelopeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Efrogg\Synergy\Entity\AbstractSynergyEntity;
use Efrogg\Synergy\Entity\SynergyNumericIdEntityTrait;
use Efrogg\Synergy\Mapping\SynergyEntity;
use Efrogg\Synergy\Mapping\SynergyFormField;

#[ORM\Entity(repositoryClass: EnvelopeRepository::class)]
#[SynergyEntity]
class Envelope extends AbstractSynergyBudgetEntity
{
    use SynergyNumericIdEntityTrait;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'envelopes')]
    #[ORM\JoinColumn(nullable: false)]
    #[SynergyFormField(ignore: true)]
    private ?Budget $budget = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'envelope')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setEnvelope($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getEnvelope() === $this) {
                $category->setEnvelope(null);
            }
        }

        return $this;
    }
}
