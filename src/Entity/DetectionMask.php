<?php

namespace App\Entity;

use App\Repository\DetectionMaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Efrogg\Synergy\Entity\SynergyEntityInterface;
use Efrogg\Synergy\Entity\SynergyNumericIdEntityTrait;
use Efrogg\Synergy\Mapping\SynergyEntity;

#[ORM\Entity(repositoryClass: DetectionMaskRepository::class)]
#[SynergyEntity]
class DetectionMask extends AbstractSynergyBudgetEntity
{
    use SynergyNumericIdEntityTrait;

    #[ORM\Column(length: 512)]
    private ?string $mask = null;

    #[ORM\ManyToOne(inversedBy: 'detectionMasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private int $score = 100;

    #[ORM\Column()]
    private bool $active = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMask(): ?string
    {
        return $this->mask;
    }

    public function setMask(string $mask): static
    {
        $this->mask = $mask;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
