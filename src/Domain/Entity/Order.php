<?php

declare(strict_types=1);

namespace ExportOrders\Domain\Entity;

use ExportOrders\Domain\ValueObject\OrderDate;
use ExportOrders\Domain\ValueObject\OrderStatus;
use ExportOrders\Domain\ValueObject\PositiveId;
use ExportOrders\Domain\ValueObject\Price;

final class Order
{
    public readonly PositiveId $itemId;
    public readonly PositiveId $customerId;
    public readonly string $comment;
    public readonly OrderStatus $status;
    public readonly OrderDate $orderDate;
    public readonly Price $price;

    public function __construct(
        PositiveId $itemId,
        PositiveId $customerId,
        string $comment,
        OrderStatus $status,
        OrderDate $orderDate,
        Price $price
    ) {
        $this->itemId = $itemId;
        $this->customerId = $customerId;
        $this->comment = $comment;
        $this->status = $status;
        $this->orderDate = $orderDate;
        $this->price = $price;
    }
}
