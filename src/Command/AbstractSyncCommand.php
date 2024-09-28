<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Sync\AbstractSynchronizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class AbstractSyncCommand extends Command
{

    protected InputInterface $input;
    protected SymfonyStyle $io;
    protected Budget $budget;

    public function __construct(
        protected readonly BudgetRepository $budgetRepository
    )
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        parent::configure();
        // add file argument
        $this->addArgument('file', InputArgument::REQUIRED, 'The file to import / export');
        // dry-run option
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Perform a dry-run');
        // budget option
        $this->addOption('budget-id', null, InputOption::VALUE_REQUIRED, 'The budget id');
        $this->addOption('budget-name', null, InputOption::VALUE_REQUIRED, 'The budget name');
    }


    /**
     * @return mixed
     */
    protected function getFile(): mixed
    {
        return $this->input->getArgument('file');
    }


    protected function configureIo(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->io = new SymfonyStyle($input, $output);

    }


    protected function prepareSync(AbstractSynchronizer $synchronizer): void
    {
        assert($this->io,'IO not configured. please call configureIo before executeImport');

        $input = $this->input;
        $io = $this->io;
        $synchronizer->setDryRun($input->getOption('dry-run'));

        // find budget
        if($input->getOption('budget-name')){
            $this->budget = $this->budgetRepository->findOneBy(['name'=>$input->getOption('budget-name')])
                ?? throw new \InvalidArgumentException('Budget not found');
        }
        else if($input->getOption('budget-id')) {
            $this->budget = $this->budgetRepository->find($input->getOption('budget-id'))
                ?? throw new \InvalidArgumentException('Budget not found');
        } else {
            throw new \InvalidArgumentException('Budget not specified');
        }
        $synchronizer->reset();
    }

}
