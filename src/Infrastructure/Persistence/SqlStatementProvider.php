<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\Persistence;

use RuntimeException;

final class SqlStatementProvider
{
    private const ALLOWED_DRIVERS = ['sqlite', 'mysql', 'postgres'];

    public function __construct(
        private readonly string $basePath,
        private readonly string $driver
    ) {
        if (!in_array($driver, self::ALLOWED_DRIVERS, true)) {
            throw new RuntimeException(sprintf(
                'Unsupported DB driver "%s". Allowed drivers: %s',
                $driver,
                implode(', ', self::ALLOWED_DRIVERS)
            ));
        }
    }

    public function schema(): string
    {
        return $this->read('schema/schema.sql');
    }

    public function testingOrdersCount(): string
    {
        return $this->read('testing/orders_count.sql');
    }

    public function clientExists(): string
    {
        return $this->read('persistence/client_exists.sql');
    }

    public function merchandisePriceById(): string
    {
        return $this->read('persistence/merchandise_price_by_id.sql');
    }

    public function orderInsert(): string
    {
        return $this->read('persistence/order_insert.sql');
    }

    private function read(string $fileName): string
    {
        $path = $this->basePath . '/' . $this->driver . '/' . $fileName;
        $sql = file_get_contents($path);

        if ($sql === false) {
            throw new RuntimeException(sprintf('Cannot read SQL statement: %s', $path));
        }

        return $sql;
    }
}
