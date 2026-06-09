<?php

declare(strict_types=1);

namespace ExportOrders\Tests\Infrastructure;

use ExportOrders\Domain\Entity\Order;
use ExportOrders\Domain\ValueObject\OrderDate;
use ExportOrders\Domain\ValueObject\OrderStatus;
use ExportOrders\Domain\ValueObject\PositiveId;
use ExportOrders\Domain\ValueObject\Price;
use ExportOrders\Infrastructure\Persistence\OrderMapper;
use PHPUnit\Framework\TestCase;

final class OrderMapperTest extends TestCase
{
    public function testItMapsOrderEntityToInsertParameters(): void
    {
        $order = new Order(
            itemId: PositiveId::fromString('3'),
            customerId: PositiveId::fromString('4'),
            comment: 'comment',
            status: OrderStatus::fromString('complete'),
            orderDate: OrderDate::fromString('2026-06-09'),
            price: Price::fromInt(999)
        );

        self::assertSame(
            [
                ':item_id' => 3,
                ':customer_id' => 4,
                ':comment' => 'comment',
                ':status' => 'complete',
                ':order_date' => '2026-06-09',
                ':price' => 999,
            ],
            (new OrderMapper())->toInsertParameters($order)
        );
    }
}
