<?php

namespace App\Command;

use App\Repository\BudgetRepository;
use App\Sync\Importer\MovementImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'budget:import:movements',
    description: 'Import movements from a file')]
class ImportMovementsCommand extends AbstractImportCommand
{
    public function __construct(
        private readonly string $movementImportBasePath,
        private readonly MovementImporter $movementImporter,
        BudgetRepository $budgetRepository
    )
    {
        parent::__construct($budgetRepository);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configureIo($input, $output);
        $this->movementImporter->setBasePath($this->movementImportBasePath);
        return $this->executeImport($this->movementImporter);
    }

}
