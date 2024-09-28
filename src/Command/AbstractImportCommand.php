<?php

namespace App\Command;

use App\Sync\Importer\AbstractImporter;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractImportCommand extends AbstractSyncCommand
{



    protected function configure(): void
    {
        parent::configure();
        $this->addOption('clear', null, InputOption::VALUE_NONE, 'Clear all movements before importing');
    }


    protected function executeImport(AbstractImporter $importer): int
    {
        $this->prepareSync($importer);

        // clear before import
        if ($this->input->getOption('clear')) {
            $importer->clear($this->budget);
        }

        // import
        $importer->import($this->getFile(), $this->budget);

        foreach ($importer->getStats()->getSubStats(true) as $statName => $stat) {
            $this->io->title($statName);
            $this->io->table([], [
                ['Imported',$stat->getImported()],
                ['Skipped',$stat->getSkipped()],
                ['Removed',$stat->getRemoved()]
            ]);
            $this->io->success(sprintf('Imported %d', $stat->getImported()));
            $this->io->info(sprintf('Skipped %d', $stat->getSkipped()));
        }

        return self::SUCCESS;
    }

}
