<?php

namespace App\Entity;

use App\Repository\DetectionMaskRepository;
use Doctrine\ORM\Mapping as ORM;
use Efrogg\Synergy\Entity\SynergyNumericIdEntityTrait;
use Efrogg\Synergy\Mapping\SynergyEntity;

#[ORM\Entity(repositoryClass: DetectionMaskRepository::class)]
#[SynergyEntity]
class DetectionMask extends AbstractSynergyBudgetEntity
{
    use SynergyNumericIdEntityTrait;

    public const string DETECTION_TYPE_CONTAINS = 'contains';
    public const string DETECTION_TYPE_REGEX = 'regex';
    public const string DETECTION_TYPE_WILDCARD = 'wildcard';

    #[ORM\Column(length: 512)]
    private string $mask = '';

    #[ORM\ManyToOne(inversedBy: 'detectionMasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private int $score = 100;

    #[ORM\Column()]
    private bool $active = true;

    #[ORM\Column(length: 255)]
    private string $name = '';

    #[ORM\Column(nullable: true)]
    private ?string $detectionType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMask(): string
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBudget():?Budget
    {
        return $this->category?->getBudget();
    }

    public function getDetectionType(): ?string
    {
        return $this->detectionType;
    }

    public function setDetectionType(?string $detectionType): static
    {
        $this->detectionType = $detectionType;

        return $this;
    }
}
