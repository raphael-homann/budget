<?php

declare(strict_types=1);

namespace App\Sync\Importer;

use App\Entity\Budget;
use App\Helper\ClearableTrait;
use App\Helper\DryRunTrait;
use App\Sync\AbstractSynchronizer;
use InvalidArgumentException;

abstract class AbstractImporter extends AbstractSynchronizer
{
    use ClearableTrait;

    /**
     * @param string $file
     *
     * @return string
     */
    protected function findFile(string $file): string
    {
        $path = $this->getFilename($file);
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('File [%s] not found', $path));
        }
        return $path;
    }

    abstract public function import(string $file, Budget $budget): void;

    abstract public function clear(Budget $budget): void;

}
