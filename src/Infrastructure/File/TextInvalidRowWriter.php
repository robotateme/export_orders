<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\File;

use ExportOrders\Application\Port\InvalidRowWriter;
use RuntimeException;

final class TextInvalidRowWriter implements InvalidRowWriter
{
    /** @var resource|null */
    private $handle = null;

    public function open(string $path): void
    {
        $handle = fopen($path, 'wb');

        if ($handle === false) {
            throw new RuntimeException(sprintf('Cannot open invalid rows file for writing: %s', $path));
        }

        $this->handle = $handle;
    }

    public function write(string $rawLine): void
    {
        if ($this->handle === null) {
            throw new RuntimeException('Invalid row writer is not open.');
        }

        fwrite($this->handle, $rawLine . PHP_EOL);
    }

    public function close(): void
    {
        if ($this->handle !== null) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
