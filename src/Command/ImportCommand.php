<?php

namespace App\Command;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Service\MovementImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'budget:import',
    description: 'Import movements from a file')]
class ImportCommand extends Command
{
    private MovementImporter $movementImporter;
    private BudgetRepository $budgetRepository;

    public function __construct(MovementImporter $movementImporter, BudgetRepository $budgetRepository, ?string $name = null)
    {
        parent::__construct($name);
        $this->movementImporter = $movementImporter;
        $this->budgetRepository = $budgetRepository;
    }

    protected function configure(): void
    {
        // add file argument
        $this->addArgument('file', InputArgument::REQUIRED, 'The file to import');
        // dry-run option
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Perform a dry-run');
        // budget option
        $this->addOption('budget-id', null, InputOption::VALUE_REQUIRED, 'The budget to import the movements to');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->movementImporter->setDryRun($input->getOption('dry-run'));
        $budget = $this->budgetRepository->find($input->getOption('budget-id'))
            ?? throw new \InvalidArgumentException('Budget not found');

        $this->movementImporter->import($input->getArgument('file'),$budget);


        return self::SUCCESS;
    }

}
