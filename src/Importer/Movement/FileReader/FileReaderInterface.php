<?php

namespace App\Importer\Movement\FileReader;

use Generator;

interface FileReaderInterface
{
    /**
     * @return Generator<array<string,mixed>>
     */
    public function read(): Generator;

}
