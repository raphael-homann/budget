<?php

namespace App\Service;

use App\Entity\Budget;
use App\Entity\Import;
use App\Entity\Movement;
use App\Helper\DryRunTrait;
use App\Importer\CreditAgricoleImporter;
use App\Importer\FileReader\FileReaderFactory;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class MovementImporter
{
    use DryRunTrait;


    public function __construct(
        private readonly string $importBasePath,
        private LoggerInterface $logger,
        private readonly CreditAgricoleImporter $creditAgricoleImporter,
        private readonly FileReaderFactory $fileReaderFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function import(string $file,Budget $budget): void
    {
        $path = $this->importBasePath . '/' . $file;
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('File [%s] not found', $path));
        }

        if ($this->isDryRun()) {
            $this->logger->info('Dry-run: Importing file ' . $file);
            return;
        }

        $this->logger->info('Importing file ' . $file);
        // do the import

        // guess the file reader based on the file extension
        $fileReader = $this->fileReaderFactory->create($path);

        $import = new Import();
        $import->setFileName($file);
        $this->entityManager->persist($import);
        $this->entityManager->flush();

        //TODO : guess the importer based on the file reader
        foreach ($this->creditAgricoleImporter->getMovements($fileReader) as $movement) {
            $movement->setBudget($budget);
            $movement->setImport($import);
            if($this->alreadyExists($movement,$budget)) {
                $this->logger->warning('Movement already exists, skipping');
                continue;
            }
            $this->entityManager->persist($movement);
//            dump($movement->getDate()->format('Y-m-d') . ' : ' . $movement->getAmount());
        }
        $this->entityManager->flush();
    }

    private function alreadyExists(Movement $newMovement, Budget $budget): bool
    {
        // check if the movement already exists
        foreach ($budget->getMovements() as $movement) {
            if ($newMovement->getDate()?->getTimestamp() === $movement->getDate()?->getTimestamp() && $newMovement->getAmount() === $movement->getAmount()) {
                return true;
            }
        }
        return false;
    }
}
