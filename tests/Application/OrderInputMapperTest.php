<?php

declare(strict_types=1);

namespace ExportOrders\Tests\Application;

use ExportOrders\Application\Mapper\OrderInputMapper;
use ExportOrders\Domain\ValueObject\OrderDate;
use ExportOrders\Domain\ValueObject\OrderStatus;
use ExportOrders\Domain\ValueObject\Price;
use PHPUnit\Framework\TestCase;

final class OrderInputMapperTest extends TestCase
{
    public function testItMapsValidInputFieldsToOrderEntity(): void
    {
        $order = (new OrderInputMapper())->toEntity(
            ['1', '2', '  comment  '],
            OrderStatus::fromString('new'),
            OrderDate::fromString('2026-06-09'),
            Price::fromInt(12345)
        );

        self::assertNotNull($order);
        self::assertSame(1, $order->itemId->value);
        self::assertSame(2, $order->customerId->value);
        self::assertSame('comment', $order->comment);
        self::assertSame('new', $order->status->value);
        self::assertSame('2026-06-09', $order->orderDate->value);
        self::assertSame(12345, $order->price->value);
    }

    public function testItReturnsNullForInvalidInputFields(): void
    {
        $order = (new OrderInputMapper())->toEntity(
            ['abc', '2', 'comment'],
            OrderStatus::fromString('new'),
            OrderDate::fromString('2026-06-09'),
            Price::fromInt(12345)
        );

        self::assertNull($order);
    }
}
