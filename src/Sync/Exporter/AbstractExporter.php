<?php

declare(strict_types=1);

namespace App\Sync\Exporter;

use App\Entity\Budget;
use App\Sync\AbstractSynchronizer;

abstract class AbstractExporter extends AbstractSynchronizer
{
    abstract public function export(string $file, Budget $budget): void;

}
