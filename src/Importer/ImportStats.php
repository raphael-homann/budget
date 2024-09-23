<?php

declare(strict_types=1);

namespace App\Importer;

class ImportStats
{

    public const string SKIPPED = 'skipped';
    public const string IMPORTED = 'imported';
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

    public function get(string $key): int
    {
        return $this->count[$key] ?? 0;
    }

    public function incrementSkipped(): void
    {
        $this->increment(self::SKIPPED);
    }
    public function incrementImported(): void
    {
        $this->increment(self::IMPORTED);
    }

    public function increment(string $key): void
    {
        $this->count[$key] = $this->get($key) + 1;
    }

    /**
     * @return array<string,int>
     */
    public function toArray(): array
    {
        return $this->count;
    }
}
