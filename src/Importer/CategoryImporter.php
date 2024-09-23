<?php

namespace App\Importer;

use App\Entity\Budget;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class CategoryImporter extends AbstractImporter
{

    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    #[Required]
    public function configurePath(string $CategoryImportBasePath): void
    {
        $this->setImportBasePath($CategoryImportBasePath);
    }

    public function import(string $file, Budget $budget): ImportStats
    {
        $stats = new ImportStats();

        $file = $this->findFile($file);
        $this->logger->notice('Importing file ' . $file);
        return $stats;
    }

    public function clear(Budget $budget): void
    {
        $this->logger->warning('Clearing all categories for budget ' . $budget->getName());
    }
}
