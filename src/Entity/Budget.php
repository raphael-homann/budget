<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Efrogg\Synergy\Entity\AbstractSynergyEntity;
use Efrogg\Synergy\Entity\NumericEntityIdInterface;
use Efrogg\Synergy\Entity\SynergyNumericIdEntityTrait;
use Efrogg\Synergy\Mapping\SynergyEntity;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
#[SynergyEntity(name: 'budget', description: 'Budget entity')]
class Budget extends AbstractSynergyBudgetEntity
{
    use SynergyNumericIdEntityTrait;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Envelope>
     */
    #[ORM\OneToMany(targetEntity: Envelope::class, mappedBy: 'budget', orphanRemoval: true)]
    private Collection $envelopes;

    /**
     * eager fetch in order to be able to find the user on deletion (for mercure event)
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'budgets', fetch: 'EAGER')]
    private Collection $users;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'budget', orphanRemoval: true)]
    private Collection $categories;

    #[ORM\Column(length: 255)]
    private string $description = '';

    /**
     * @var Collection<int, Movement>
     */
    #[ORM\OneToMany(targetEntity: Movement::class, mappedBy: 'budget', orphanRemoval: true)]
    private Collection $movements;

    public function __construct()
    {
        $this->envelopes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    /**
     * @return Collection<int, Envelope>
     */
    public function getEnvelopes(): Collection
    {
        return $this->envelopes;
    }

    public function addEnvelope(Envelope $envelope): static
    {
        if (!$this->envelopes->contains($envelope)) {
            $this->envelopes->add($envelope);
            $envelope->setBudget($this);
        }

        return $this;
    }

    public function removeEnvelope(Envelope $envelope): static
    {
        if ($this->envelopes->removeElement($envelope)) {
            // set the owning side to null (unless already changed)
            if ($envelope->getBudget() === $this) {
                $envelope->setBudget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

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
            $category->setBudget($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getBudget() === $this) {
                $category->setBudget(null);
            }
        }

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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
            $movement->setBudget($this);
        }

        return $this;
    }

    public function removeMovement(Movement $movement): static
    {
        if ($this->movements->removeElement($movement)) {
            // set the owning side to null (unless already changed)
            if ($movement->getBudget() === $this) {
                $movement->setBudget(null);
            }
        }

        return $this;
    }
}
