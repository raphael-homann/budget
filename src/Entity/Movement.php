<?php

namespace App\Entity;

use App\Repository\MovementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Efrogg\Synergy\Entity\SynergyNumericIdEntityTrait;
use Efrogg\Synergy\Mapping\SynergyEntity;

#[ORM\Entity(repositoryClass: MovementRepository::class)]
#[SynergyEntity]
class Movement extends AbstractSynergyBudgetEntity
{
    use SynergyNumericIdEntityTrait;

    #[ORM\Column(type: Types::FLOAT, precision: 10, scale: 2)]
    private ?float $amount = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'movements')]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(length: 255)]
    private string $comment = '';

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: Budget::class, inversedBy: 'movements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Budget $budget = null;

    #[ORM\ManyToOne(inversedBy: 'movements')]
    private ?Import $import = null;

    #[ORM\ManyToOne]
    private ?DetectionMask $detectionMask = null;

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }



    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

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

    public function getImport(): ?Import
    {
        return $this->import;
    }

    public function setImport(?Import $import): static
    {
        $this->import = $import;

        return $this;
    }

    public function getDetectionMask(): ?DetectionMask
    {
        return $this->detectionMask;
    }

    public function setDetectionMask(?DetectionMask $detectionMask): static
    {
        $this->detectionMask = $detectionMask;

        return $this;
    }
}
