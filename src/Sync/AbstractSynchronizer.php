<?php

declare(strict_types=1);

namespace App\Sync;

use App\Helper\DryRunTrait;

class AbstractSynchronizer
{
    use DryRunTrait;

    protected string $basePath;

    protected SyncStats $stats;


    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath(string $basePath): void
    {
        $this->basePath = $basePath;
    }

    public function reset(): void
    {
        $this->stats = new SyncStats();
    }

    /**
     * @return SyncStats
     */
    public function getStats(): SyncStats
    {
        return $this->stats;
    }


    public function fileExists(string $file): bool
    {
        return file_exists($this->getFilename($file));
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function getFilename(string $file): string
    {
        return $this->basePath . '/' . $file;
    }


}
