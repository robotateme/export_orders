<?php

declare(strict_types=1);

namespace ExportOrders\Application\Port;

use Generator;

interface OrderRowReader
{
    /**
     * @return Generator<\ExportOrders\Application\Dto\OrderInputRow>
     */
    public function read(string $path): Generator;
}
