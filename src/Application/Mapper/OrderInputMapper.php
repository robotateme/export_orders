<?php

declare(strict_types=1);

namespace ExportOrders\Application\Mapper;

use ExportOrders\Domain\Entity\Order;
use ExportOrders\Domain\ValueObject\OrderDate;
use ExportOrders\Domain\ValueObject\OrderStatus;
use ExportOrders\Domain\ValueObject\PositiveId;
use ExportOrders\Domain\ValueObject\Price;
use InvalidArgumentException;

final class OrderInputMapper
{
    /**
     * @param list<string> $fields
     */
    public function itemId(array $fields): ?PositiveId
    {
        if (count($fields) !== 3) {
            return null;
        }

        try {
            return PositiveId::fromString($fields[0]);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * @param list<string> $fields
     */
    public function toEntity(array $fields, OrderStatus $status, OrderDate $orderDate, Price $price): ?Order
    {
        if (count($fields) !== 3) {
            return null;
        }

        try {
            $itemId = PositiveId::fromString($fields[0]);
            $customerId = PositiveId::fromString($fields[1]);
        } catch (InvalidArgumentException) {
            return null;
        }

        return new Order(
            itemId: $itemId,
            customerId: $customerId,
            comment: trim($fields[2]),
            status: $status,
            orderDate: $orderDate,
            price: $price
        );
    }
}
