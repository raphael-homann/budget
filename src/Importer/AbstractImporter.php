<?php

declare(strict_types=1);

namespace App\Importer;

use App\Entity\Budget;
use App\Helper\ClearableTrait;
use App\Helper\DryRunTrait;
use InvalidArgumentException;

abstract class AbstractImporter
{
    use DryRunTrait;
    use ClearableTrait;

    private string $importBasePath;

    /**
     * @param string $file
     *
     * @return string
     */
    protected function findFile(string $file): string
    {
        $path = $this->importBasePath . '/' . $file;
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('File [%s] not found', $path));
        }
        return $path;
    }

    /**
     * @return string
     */
    public function getImportBasePath(): string
    {
        return $this->importBasePath;
    }

    /**
     * @param string $importBasePath
     */
    public function setImportBasePath(string $importBasePath): void
    {
        $this->importBasePath = $importBasePath;
    }

    abstract public function import(string $file, Budget $budget): ImportStats;
    abstract public function clear(Budget $budget): void;

}
