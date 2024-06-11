<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Envelope>
     */
    #[ORM\OneToMany(targetEntity: Envelope::class, mappedBy: 'budget', orphanRemoval: true)]
    private Collection $envelopes;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'budgets')]
    private Collection $user;

    public function __construct()
    {
        $this->envelopes = new ArrayCollection();
        $this->user = new ArrayCollection();
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
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }
}
