<?php

declare(strict_types=1);

namespace ExportOrders\Application\Port;

interface InvalidRowWriter
{
    public function open(string $path): void;

    public function write(string $rawLine): void;

    public function close(): void;
}
