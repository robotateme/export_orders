<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\File;

use ExportOrders\Application\Dto\OrderInputRow;
use ExportOrders\Application\Port\OrderRowReader;
use Generator;
use RuntimeException;

final class SemicolonOrderRowReader implements OrderRowReader
{
    public function read(string $path): Generator
    {
        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('Input file is not readable: %s', $path));
        }

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException(sprintf('Cannot open input file: %s', $path));
        }

        try {
            $lineNumber = 0;

            while (($line = fgets($handle)) !== false) {
                $lineNumber++;
                $rawLine = rtrim($line, "\r\n");

                yield new OrderInputRow(
                    lineNumber: $lineNumber,
                    rawLine: $rawLine,
                    fields: array_map(
                        static fn (?string $field): string => $field ?? '',
                        str_getcsv($rawLine, ';', '"', '')
                    )
                );
            }

            if (!feof($handle)) {
                throw new RuntimeException('Unexpected error while reading input file.');
            }
        } finally {
            fclose($handle);
        }
    }
}
