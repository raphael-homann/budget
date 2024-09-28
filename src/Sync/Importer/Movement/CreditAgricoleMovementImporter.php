<?php

namespace App\Sync\Importer\Movement;

use App\Entity\Movement;
use App\Sync\Importer\Movement\FileReader\FileReaderInterface;
use DateTimeImmutable;
use Exception;
use Generator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class CreditAgricoleMovementImporter implements MovementImporterInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    const string HEADER_DATE = 'Date';
    const string HEADER_LABEL = 'Libellé';
    const string HEADER_DEBT = 'Débit euros';
    const string HEADER_CREDIT = 'Crédit euros';

    private const array HEADERS = [self::HEADER_DATE, self::HEADER_LABEL];
    /**
     * @var array<string>
     */
    protected array $headers;

    /**
     * @param FileReaderInterface $fileReader
     *
     * @return Generator<Movement>
     * @throws \Exception
     */
    public function getMovements(FileReaderInterface $fileReader): Generator
    {
        foreach ($fileReader->read() as $row) {
            if (!$this->waitForHeader($row)) {
                continue;
            }
            $associated = array_combine($this->headers, $row);

            $amount = $associated[self::HEADER_DEBT] ? -$associated[self::HEADER_DEBT] : $associated[self::HEADER_CREDIT];
            if (!$amount) {
                $this->logger?->warning('No amount found in row', $row);
                continue;
            }
            try {
                $date = new DateTimeImmutable($associated[self::HEADER_DATE]);
            } catch (Exception $e) {
                $this->logger?->warning(sprintf('Invalid date [%s] found in row ', $associated[self::HEADER_DATE]));
                continue;
            }
            $label = $associated[self::HEADER_LABEL];


            $movement = new Movement();
            $movement->setDate($date);
            $movement->setLabel($label);
            $movement->setAmount($amount);

            yield $movement;
        }
    }


    /**
     * @param array<string> $row
     *
     * @return bool
     * return true when the header is found
     * return false when the header is not found, event for the header row
     */
    private function waitForHeader(array $row): bool
    {
        if (isset($this->headers)) {
            return true;
        }
        // check if all headers are present
        $presentHeaders = array_intersect($row, self::HEADERS);
        if (count($presentHeaders) === count(self::HEADERS)) {
            $this->headers = $row;
            // return false to skip the header row
        }
        return false;
    }
}
