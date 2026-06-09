<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\Persistence;

use ExportOrders\Application\Port\OrderRepository;
use ExportOrders\Domain\Entity\Order;
use PDO;
use PDOStatement;

final class PdoOrderRepository implements OrderRepository
{
    private PDOStatement $insert;
    private OrderMapper $mapper;

    public function __construct(PDO $pdo, string $insertSql, ?OrderMapper $mapper = null)
    {
        $this->insert = $pdo->prepare($insertSql);
        $this->mapper = $mapper ?? new OrderMapper();
    }

    public function add(Order $order): void
    {
        $this->insert->execute($this->mapper->toInsertParameters($order));
    }
}
