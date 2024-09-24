<?php

declare(strict_types=1);

namespace App\Importer;

class ImportStats
{

    /**
     * @var array<string, ImportStats>
     */
    public array $subStats = [];
    public const string SKIPPED = 'skipped';
    public const string IMPORTED = 'imported';
    public const string REMOVED = 'removed';
    /**
     * @var array<string, int>
     */
    private array $count = [];

    public function getSkipped(): int
    {
        return $this->get(self::SKIPPED);
    }

    public function getImported(): int
    {
        return $this->get(self::IMPORTED);
    }

    public function getRemoved(): int
    {
        return $this->get(self::REMOVED);
    }

    public function get(string $key): int
    {
        return $this->count[$key] ?? 0;
    }

    public function incrementSkipped(?string $index = null): void
    {
        $this->increment(self::SKIPPED, 1, $index);
    }

    public function incrementImported(?string $index = null): void
    {
        $this->increment(self::IMPORTED, 1, $index);
    }

    public function incrementRemoved(?string $index = null): void
    {
        $this->increment(self::REMOVED, 1, $index);
    }

    public function increment(string $key, int $increment = 1, ?string $index = null): void
    {
        $stat = $this->findStat($index);
        $stat->count[$key] = $stat->get($key) + $increment;
    }

    /**
     * @return array<string,int>
     */
    public function toArray(?string $index = null): array
    {
        return $this->findStat($index)->count;
    }

    private function findStat(?string $index): self
    {
        if (null === $index) {
            return $this;
        }
        if (!isset($this->subStats[$index])) {
            $this->subStats[$index] = new self();
        }
        return $this->subStats[$index];
    }

    /**
     * @return array<ImportStats>
     */
    public function getSubStats(bool $includingMain = false): array
    {
        $stats = $this->subStats;
        if ($includingMain) {
            $stats[''] = $this;
        }
        return $stats;
    }

}
