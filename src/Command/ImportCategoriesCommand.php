<?php

namespace App\Command;

use App\Importer\CategoryImporter;
use App\Repository\BudgetRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'budget:import:categories',
    description: 'Import Categories from a file')]
class ImportCategoriesCommand extends AbstractImportCommand
{

    public function __construct(
        private readonly CategoryImporter $categoryImporter,
        BudgetRepository $budgetRepository
    )
    {
        parent::__construct($budgetRepository);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configureIo($input,$output);

        return $this->executeImport($this->categoryImporter);

    }
}
