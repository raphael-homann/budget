<?php

namespace App\Importer;

use App\Entity\Movement;
use App\Importer\FileReader\FileReaderInterface;
use DateTimeImmutable;
use Exception;
use Generator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class CreditAgricoleImporter implements ImporterInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    const string HEADER_DATE = 'Date';
    const string HEADER_LABEL = 'Libellé';
    const string HEADER_DEBT = 'Débit euros';
    const string HEADER_CREDIT = 'Crédit euros';

    private const HEADERS = [self::HEADER_DATE, self::HEADER_LABEL];
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
            $movement->setComment($label);
            $movement->setAmount($amount);

            yield $movement;
        }
    }


    private function waitForHeader(array $row): bool
    {
        if (isset($this->headers)) {
            return true;
        }
        if (in_array(self::HEADER_DATE, $row, true) && in_array(self::HEADER_LABEL, $row, true)) {
            $this->headers = $row;
            return false;
        }
        return false;
    }
}
