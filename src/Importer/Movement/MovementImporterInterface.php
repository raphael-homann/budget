<?php

namespace App\Importer\Movement;

use App\Entity\Movement;
use App\Importer\Movement\FileReader\FileReaderInterface;
use Generator;

interface MovementImporterInterface
{

    /**
     * @param FileReaderInterface $fileReader
     *
     * @return Generator<Movement>
     */
    public function getMovements(FileReaderInterface $fileReader): Generator;
}
