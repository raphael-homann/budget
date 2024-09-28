<?php

declare(strict_types=1);

namespace App\Sync\Importer;

use App\Entity\Budget;
use App\Entity\Import;
use App\Entity\Movement;
use App\Repository\MovementRepository;
use App\Sync\Importer\Movement\CreditAgricoleMovementImporter;
use App\Sync\Importer\Movement\FileReader\FileReaderFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class MovementImporter extends AbstractImporter
{

    public function __construct(
        private LoggerInterface $logger,
        private readonly CreditAgricoleMovementImporter $creditAgricoleImporter,
        private readonly FileReaderFactory $fileReaderFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly MovementRepository $movementRepository,
    ) {
    }

    #[Required]
    public function configurePath(string $movementImportBasePath): void
    {
        $this->setBasePath($movementImportBasePath);
    }

    public function import(string $file,Budget $budget): void
    {
        $path = $this->findFile($file);

        if ($this->isDryRun()) {
            $this->logger->info('Dry-run: Importing file ' . $file);
        }

        $this->logger->info('Importing file ' . $file);
        // do the import

        // guess the file reader based on the file extension
        $fileReader = $this->fileReaderFactory->create($path);

        // prepare import entity
        $import = new Import();
        $import->setFileName($file);
        $import->setBudget($budget);
        $import->setClear($this->isClear());
        $this->entityManager->persist($import);
        $this->entityManager->flush();

        $this->logger->notice('importing movements');
        //TODO : guess the importer based on the file reader
        foreach ($this->creditAgricoleImporter->getMovements($fileReader) as $movement) {
            $movement->setBudget($budget);
            $movement->setImport($import);
            if($this->alreadyExists($movement,$budget)) {
                $this->stats->incrementSkipped();
                $this->logger->warning('Movement already exists, skipping');
                continue;
            }
            $this->stats->incrementImported();
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

    public function clear(Budget $budget): void
    {
        $this->logger->warning('Clearing all movements');
        $movements = $this->movementRepository->findBy(['budget' => $budget]);
        $this->logger->info('found ' . count($movements) . ' movements');

        // dry run mode
        if ($this->isDryRun()) {
            $this->logger->info('Dry-run: would remove ' . count($movements) . ' movements');
            return;
        }

        // real mode
        array_map(fn(Movement $movement) => $this->entityManager->remove($movement), $movements);
        $this->entityManager->flush();
    }
}
