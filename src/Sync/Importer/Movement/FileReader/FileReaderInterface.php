<?php

namespace App\Sync\Importer\Movement\FileReader;

use Generator;

interface FileReaderInterface
{
    /**
     * @return Generator<array<string,mixed>>
     */
    public function read(): Generator;

}
