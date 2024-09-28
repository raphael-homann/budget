<?php

declare(strict_types=1);

namespace App\Sync;

class SyncStats
{

    /**
     * @var array<string, SyncStats>
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

    public function incrementSkipped(?string $index = null, int $increment = 1): void
    {
        $this->increment(self::SKIPPED, $index, $increment);
    }

    public function incrementImported(?string $index = null, int $increment = 1): void
    {
        $this->increment(self::IMPORTED, $index, $increment);
    }

    public function incrementRemoved(?string $index = null, int $increment = 1): void
    {
        $this->increment(self::REMOVED, $index, $increment);
    }

    public function increment(string $key, ?string $index = null, int $increment = 1): void
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
     * @return array<SyncStats>
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
