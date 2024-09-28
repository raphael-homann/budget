<?php

namespace App\Command;

use App\Repository\BudgetRepository;
use App\Sync\Exporter\CategoryExporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'budget:export:categories',
    description: 'Export Categories to a file')]
class ExportCategoriesCommand extends AbstractExportCommand
{

    public function __construct(
        private readonly CategoryExporter $categoryExporter,
        BudgetRepository $budgetRepository
    ) {
        parent::__construct($budgetRepository);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->configureIo($input, $output);

        return $this->executeExport($this->categoryExporter);
    }
}
