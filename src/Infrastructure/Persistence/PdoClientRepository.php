<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\Persistence;

use ExportOrders\Application\Port\ClientRepository;
use ExportOrders\Domain\ValueObject\PositiveId;
use PDO;
use PDOStatement;

final class PdoClientRepository implements ClientRepository
{
    private PDOStatement $exists;

    public function __construct(PDO $pdo, string $existsSql)
    {
        $this->exists = $pdo->prepare($existsSql);
    }

    public function exists(PositiveId $id): bool
    {
        $this->exists->execute([':id' => $id->value]);

        return $this->exists->fetchColumn() !== false;
    }
}
