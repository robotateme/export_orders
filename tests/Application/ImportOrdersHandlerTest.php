<?php

declare(strict_types=1);

namespace ExportOrders\Tests\Application;

use ExportOrders\Application\Command\ImportOrdersCommand;
use ExportOrders\Application\Command\ImportOrdersHandler;
use ExportOrders\Infrastructure\File\SemicolonOrderRowReader;
use ExportOrders\Infrastructure\File\TextInvalidRowWriter;
use ExportOrders\Infrastructure\Persistence\PdoClientRepository;
use ExportOrders\Infrastructure\Persistence\PdoMerchandiseRepository;
use ExportOrders\Infrastructure\Persistence\PdoOrderRepository;
use ExportOrders\Infrastructure\Persistence\SqlStatementProvider;
use PDO;
use PHPUnit\Framework\TestCase;

final class ImportOrdersHandlerTest extends TestCase
{
    private string $databasePath;
    private string $invalidPath;

    protected function setUp(): void
    {
        $this->databasePath = sys_get_temp_dir() . '/export_orders_test_' . uniqid('', true) . '.sqlite';
        $this->invalidPath = sys_get_temp_dir() . '/export_orders_invalid_' . uniqid('', true) . '.txt';
    }

    protected function tearDown(): void
    {
        foreach ([$this->databasePath, $this->invalidPath] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
    }

    public function testItImportsValidRowsAndWritesInvalidRows(): void
    {
        $pdo = $this->createDatabase();
        $sql = new SqlStatementProvider(__DIR__ . '/../../database/sql', 'sqlite');
        $handler = new ImportOrdersHandler(
            new SemicolonOrderRowReader(),
            new TextInvalidRowWriter(),
            new PdoClientRepository($pdo, $sql->clientExists()),
            new PdoMerchandiseRepository($pdo, $sql->merchandisePriceById()),
            new PdoOrderRepository($pdo, $sql->orderInsert()),
            $pdo
        );

        $result = $handler->handle(new ImportOrdersCommand(
            inputPath: __DIR__ . '/../../data/orders_input.txt',
            invalidPath: $this->invalidPath,
            status: 'new',
            orderDate: '2026-06-09'
        ));

        self::assertSame(10, $result->processed);
        self::assertSame(4, $result->imported);
        self::assertSame(6, $result->invalid);
        $sql = new SqlStatementProvider(__DIR__ . '/../../database/sql', 'sqlite');
        $orderCount = $pdo->query($sql->testingOrdersCount());

        self::assertNotFalse($orderCount);
        self::assertSame(4, (int) $orderCount->fetchColumn());
        self::assertSame(
            file_get_contents(__DIR__ . '/../../data/expected_invalid_orders.txt'),
            file_get_contents($this->invalidPath)
        );
    }

    private function createDatabase(): PDO
    {
        $pdo = new PDO('sqlite:' . $this->databasePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = new SqlStatementProvider(__DIR__ . '/../../database/sql', 'sqlite');
        $pdo->exec($sql->schema());

        return $pdo;
    }
}
