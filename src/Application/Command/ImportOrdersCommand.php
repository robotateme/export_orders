<?php

declare(strict_types=1);

namespace ExportOrders\Application\Command;

use ExportOrders\Domain\ValueObject\OrderDate;
use ExportOrders\Domain\ValueObject\OrderStatus;

final class ImportOrdersCommand
{
    public readonly string $inputPath;
    public readonly string $invalidPath;
    public OrderStatus $status;
    public OrderDate $orderDate;

    public function __construct(
        string $inputPath,
        string $invalidPath,
        string $status,
        string $orderDate
    ) {
        $this->inputPath = $inputPath;
        $this->invalidPath = $invalidPath;
        $this->status = OrderStatus::fromString($status);
        $this->orderDate = OrderDate::fromString($orderDate);
    }
}
