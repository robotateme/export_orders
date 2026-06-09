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
use Dotenv\Dotenv;

$composerAutoload = __DIR__ . '/vendor/autoload.php';
require is_file($composerAutoload) ? $composerAutoload : __DIR__ . '/src/Shared/autoload.php';

if (class_exists(Dotenv::class)) {
    Dotenv::createImmutable(__DIR__)->safeLoad();
}

const EXIT_USAGE = 2;
const EXIT_RUNTIME = 1;

function usage(): string
{
    return <<<TEXT
Usage:
  php import_orders.php --input=<file> --invalid=<file> [options]

Required via CLI option or .env:
  --dsn       PDO DSN. Env: DB_DSN
  --input     Source file with rows: item_id;customer_id;comment
              Env: IMPORT_INPUT_PATH
  --invalid   Destination file for invalid source rows
              Env: IMPORT_INVALID_PATH

Options:
  --user      Database user. Env: DB_USER
  --password  Database password. Env: DB_PASSWORD
  --status    Order status for imported rows, default: new. Env: IMPORT_STATUS
  --date      Order date in YYYY-MM-DD format, default: today. Env: IMPORT_ORDER_DATE
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

function envString(string $key): ?string
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if (!is_string($value) || $value === '') {
        return null;
    }

    return $value;
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

$dsn = optionString($options, 'dsn') ?? envString('DB_DSN');
$inputPath = optionString($options, 'input') ?? envString('IMPORT_INPUT_PATH');
$invalidPath = optionString($options, 'invalid') ?? envString('IMPORT_INVALID_PATH');
$user = optionString($options, 'user') ?? envString('DB_USER');
$password = optionString($options, 'password') ?? envString('DB_PASSWORD');
$status = optionString($options, 'status') ?? envString('IMPORT_STATUS') ?? 'new';
$orderDate = optionString($options, 'date') ?? envString('IMPORT_ORDER_DATE') ?? date('Y-m-d');

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
