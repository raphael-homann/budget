<?php

namespace App\Importer\FileReader;

use Generator;

class CsvFileReader implements FileReaderInterface
{

    /**
     * @param string $file
     */
    public function __construct(private readonly string $file)
    {
    }

    public function read(): Generator
    {
        $file = fopen($this->file, 'r');

        while (($data = fgetcsv($file)) !== false) {
            yield $data;
        }

        fclose($file);
    }
}
