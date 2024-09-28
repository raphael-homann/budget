<?php

namespace App\Sync\Importer\Movement\FileReader;

class FileReaderFactory
{
    public function create(string $file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);

        switch ($extension) {
            case 'csv':
                return new CsvFileReader($file);
            case 'xlsx':
                return new ExcelFileReader($file);
            default:
                throw new \InvalidArgumentException('Unsupported file extension');
        }
    }
}
