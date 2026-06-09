#!/usr/bin/env php
<?php

declare(strict_types=1);

use ExportOrders\Application\Command\ImportOrdersCommand;
use ExportOrders\Application\Command\ImportOrdersHandler;
use ExportOrders\Infrastructure\File\SemicolonOrderRowReader;
use ExportOrders\Infrastructure\File\TextInvalidRowWriter;
use ExportOrders\Infrastructure\Persistence\PdoClientRepository;
use ExportOrders\Infrastructure\Persistence\PdoMerchandiseRepository;
use ExportOrders\Infrastructure\Persistence\PdoOrderRepository;
use ExportOrders\Infrastructure\Persistence\SqlStatementProvider;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
require is_file($composerAutoload) ? $composerAutoload : __DIR__ . '/src/Shared/autoload.php';

const EXIT_USAGE = 2;
const EXIT_RUNTIME = 1;

function usage(): string
{
    return <<<TEXT
Usage:
  php import_orders.php --dsn=<pdo-dsn> --input=<file> --invalid=<file> [options]

Required:
  --dsn       PDO DSN, for example "mysql:host=127.0.0.1;dbname=shop;charset=utf8mb4"
              or "sqlite:/absolute/path/shop.sqlite"
  --input     Source file with rows: item_id;customer_id;comment
  --invalid   Destination file for invalid source rows

Options:
  --user      Database user
  --password  Database password
  --status    Order status for imported rows, default: new
  --date      Order date in YYYY-MM-DD format, default: today
  --help      Show this help

TEXT;
}

function fail(string $message, int $code = EXIT_RUNTIME): never
{
    fwrite(STDERR, $message . PHP_EOL);
    exit($code);
}

/**
 * @param array<string, list<mixed>|string|false> $options
 */
function optionString(array $options, string $key): ?string
{
    $value = $options[$key] ?? null;

    return is_string($value) ? $value : null;
}

$options = getopt('', [
    'dsn:',
    'user::',
    'password::',
    'input:',
    'invalid:',
    'status::',
    'date::',
    'help',
]);

if ($options === false) {
    fail(usage(), EXIT_USAGE);
}

if (isset($options['help'])) {
    echo usage();
    exit(0);
}

$dsn = optionString($options, 'dsn');
$inputPath = optionString($options, 'input');
$invalidPath = optionString($options, 'invalid');
$user = optionString($options, 'user');
$password = optionString($options, 'password');
$status = optionString($options, 'status') ?? 'new';
$orderDate = optionString($options, 'date') ?? date('Y-m-d');

if ($dsn === null || $inputPath === null || $invalidPath === null) {
    fail(usage(), EXIT_USAGE);
}

try {
    $pdo = new PDO(
        $dsn,
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $sql = new SqlStatementProvider(__DIR__ . '/database/sql/persistence');

    $handler = new ImportOrdersHandler(
        new SemicolonOrderRowReader(),
        new TextInvalidRowWriter(),
        new PdoClientRepository($pdo, $sql->clientExists()),
        new PdoMerchandiseRepository($pdo, $sql->merchandisePriceById()),
        new PdoOrderRepository($pdo, $sql->orderInsert()),
        $pdo
    );

    $result = $handler->handle(new ImportOrdersCommand(
        inputPath: $inputPath,
        invalidPath: $invalidPath,
        status: $status,
        orderDate: $orderDate
    ));

    printf(
        "Processed: %d\nImported: %d\nInvalid: %d\n",
        $result->processed,
        $result->imported,
        $result->invalid
    );
} catch (Throwable $e) {
    fail($e->getMessage(), $e instanceof InvalidArgumentException ? EXIT_USAGE : EXIT_RUNTIME);
}
