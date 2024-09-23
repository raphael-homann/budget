<?php

namespace App\Command;

use App\Importer\AbstractImporter;
use App\Repository\BudgetRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractImportCommand extends Command
{

    protected InputInterface $input;
    protected SymfonyStyle $io;

    public function __construct(
        protected readonly BudgetRepository $budgetRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        // add file argument
        $this->addArgument('file', InputArgument::REQUIRED, 'The file to import');
        // dry-run option
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Perform a dry-run');
        // budget option
        $this->addOption('budget-id', null, InputOption::VALUE_REQUIRED, 'The budget to import the movements to');
        $this->addOption('budget-name', null, InputOption::VALUE_REQUIRED, 'The budget name to import the movements to');
        $this->addOption('clear', null, InputOption::VALUE_NONE, 'Clear all movements before importing');
    }

    protected function configureIo(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->io = new SymfonyStyle($input, $output);

    }
    protected function executeImport(AbstractImporter $importer): int
    {
        $this->io?? throw new \LogicException('IO not configured. please call configureIo before executeImport');

        $input = $this->input;
        $io = $this->io;
        $importer->setDryRun($input->getOption('dry-run'));

        // find budget
        if($input->getOption('budget-name')){
            $budget = $this->budgetRepository->findOneBy(['name'=>$input->getOption('budget-name')])
                ?? throw new \InvalidArgumentException('Budget not found');
        }
        else if($input->getOption('budget-id')) {
            $budget = $this->budgetRepository->find($input->getOption('budget-id'))
                ?? throw new \InvalidArgumentException('Budget not found');
        } else {
            throw new \InvalidArgumentException('Budget not specified');
        }

        // clear before import
        if($input->getOption('clear')){
            $importer->clear($budget);
        }

        // import
        $stats = $importer->import($input->getArgument('file'),$budget);

        $io->table([],[
            ['Imported',$stats->getImported()],
            ['Skipped',$stats->getSkipped()],
        ]);
        $io->success(sprintf('Imported %d',$stats->getImported()));
        $io->info(sprintf('Skipped %d',$stats->getSkipped()));

        return self::SUCCESS;
    }

}
