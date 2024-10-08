<?php

namespace App\Sync\Importer\Movement\FileReader;

use Generator;
use Shuchkin\SimpleXLSX;

class ExcelFileReader implements FileReaderInterface
{

    /**
     * @param string $filename
     */
    public function __construct(private readonly string $filename)
    {
    }

    public function read(): Generator
    {
        $xls = SimpleXLSX::parseFile( $this->filename, $debug = false );

        if(!$xls) {
            throw new \InvalidArgumentException('Invalid file');
        }

        foreach ($xls->rows() as $row) {
            yield $row;
        }
    }
}
