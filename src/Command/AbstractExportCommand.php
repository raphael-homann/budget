<?php

declare(strict_types=1);

namespace App\Command;

use App\Sync\Exporter\AbstractExporter;

abstract class AbstractExportCommand extends AbstractSyncCommand
{
    protected function executeExport(AbstractExporter $exporter): int
    {
        $this->prepareSync($exporter);
        $exporter->export($this->getFile(), $this->budget);
        dump($exporter->getStats());
        return self::SUCCESS;
    }

}
