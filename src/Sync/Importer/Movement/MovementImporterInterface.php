<?php

namespace App\Sync\Importer\Movement;

use App\Entity\Movement;
use App\Sync\Importer\Movement\FileReader\FileReaderInterface;
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
