<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\Persistence;

use ExportOrders\Domain\Entity\Order;

final class OrderMapper
{
    /**
     * @return array{
     *     ':item_id': int,
     *     ':customer_id': int,
     *     ':comment': string,
     *     ':status': string,
     *     ':order_date': string,
     *     ':price': int
     * }
     */
    public function toInsertParameters(Order $order): array
    {
        return [
            ':item_id' => $order->itemId->value,
            ':customer_id' => $order->customerId->value,
            ':comment' => $order->comment,
            ':status' => $order->status->value,
            ':order_date' => $order->orderDate->value,
            ':price' => $order->price->value,
        ];
    }
}
