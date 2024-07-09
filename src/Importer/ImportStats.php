<?php

namespace App\Importer;

class ImportStats
{

    private int $skipped = 0;
    private int $imported = 0;


    public function getSkipped(): int
    {
        return $this->skipped;
    }

    public function getImported(): int
    {
        return $this->imported;
    }

    public function incrementSkipped(): void
    {
        $this->skipped++;
    }

    public function incrementImported(): void
    {
        $this->imported++;
    }

    public function toArray(): array
    {
        return [
            'skipped' => $this->skipped,
            'imported' => $this->imported,
        ];
    }
}
