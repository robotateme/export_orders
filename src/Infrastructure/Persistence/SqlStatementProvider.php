<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\Persistence;

use RuntimeException;

final class SqlStatementProvider
{
    public function __construct(private readonly string $basePath)
    {
    }

    public function clientExists(): string
    {
        return $this->read('client_exists.sql');
    }

    public function merchandisePriceById(): string
    {
        return $this->read('merchandise_price_by_id.sql');
    }

    public function orderInsert(): string
    {
        return $this->read('order_insert.sql');
    }

    private function read(string $fileName): string
    {
        $path = $this->basePath . '/' . $fileName;
        $sql = file_get_contents($path);

        if ($sql === false) {
            throw new RuntimeException(sprintf('Cannot read SQL statement: %s', $path));
        }

        return $sql;
    }
}
