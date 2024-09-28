<?php

declare(strict_types=1);

namespace App\Sync;

trait overWriteTrait
{

    private bool $overWrite = false;

    public function setOverWrite(bool $overWrite): void
    {
        $this->overWrite = $overWrite;
    }

    public function isOverWrite(): bool
    {
        return $this->overWrite;
    }
}
