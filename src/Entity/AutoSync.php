<?php

namespace App\Entity;

use App\Repository\AutoSyncRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutoSyncRepository::class)]
class AutoSync
{
    #[ORM\Id]
    #[ORM\Column]
    private string $id;

    #[ORM\Column(length: 255)]
    private ?string $topic = null;

    #[ORM\Column(type: 'json')]
    private array $criteriaCollection = [];


    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): static
    {
        $this->topic = $topic;

        return $this;
    }

    public function getCriteriaCollection(): array
    {
        return $this->criteriaCollection;
    }

    public function setCriteriaCollection(array $criteriaCollection): static
    {
        $this->criteriaCollection = $criteriaCollection;

        return $this;
    }
}
