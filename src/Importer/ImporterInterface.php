<?php

namespace App\Importer;

use App\Entity\Movement;
use App\Importer\FileReader\FileReaderInterface;
use Generator;

interface ImporterInterface
{

    /**
     * @param FileReaderInterface $fileReader
     *
     * @return Generator<Movement>
     */
    public function getMovements(FileReaderInterface $fileReader): Generator;
}
