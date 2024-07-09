<?php

namespace App\Service;

use App\Entity\Budget;
use App\Entity\Import;
use App\Entity\Movement;
use App\Helper\DryRunTrait;
use App\Importer\CreditAgricoleImporter;
use App\Importer\FileReader\FileReaderFactory;
use App\Importer\ImportStats;
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

    /**
     * @return string
     */
    public function getImportBasePath(): string
    {
        return $this->importBasePath;
    }

    public function import(string $file,Budget $budget): ImportStats
    {
        $stats = new ImportStats();
        $path = $this->importBasePath . '/' . $file;
        if (!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('File [%s] not found', $path));
        }

        if ($this->isDryRun()) {
            $this->logger->info('Dry-run: Importing file ' . $file);
            $stats;
        }

        $this->logger->info('Importing file ' . $file);
        // do the import

        // guess the file reader based on the file extension
        $fileReader = $this->fileReaderFactory->create($path);

        $import = new Import();
        $import->setFileName($file);
        $import->setBudget($budget);
        $this->entityManager->persist($import);
        $this->entityManager->flush();

        //TODO : guess the importer based on the file reader
        foreach ($this->creditAgricoleImporter->getMovements($fileReader) as $movement) {
            $movement->setBudget($budget);
            $movement->setImport($import);
            if($this->alreadyExists($movement,$budget)) {
                $stats->incrementSkipped();
                $this->logger->warning('Movement already exists, skipping');
                continue;
            }
            $stats->incrementImported();
            $this->entityManager->persist($movement);
//            dump($movement->getDate()->format('Y-m-d') . ' : ' . $movement->getAmount());
        }
        $this->entityManager->flush();

        return $stats;
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
