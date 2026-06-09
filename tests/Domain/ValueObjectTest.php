<?php

declare(strict_types=1);

namespace ExportOrders\Tests\Domain;

use ExportOrders\Domain\ValueObject\OrderDate;
use ExportOrders\Domain\ValueObject\OrderStatus;
use ExportOrders\Domain\ValueObject\PositiveId;
use ExportOrders\Domain\ValueObject\Price;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ValueObjectTest extends TestCase
{
    public function testPositiveIdAcceptsPositiveIntegerString(): void
    {
        self::assertSame(42, PositiveId::fromString('42')->value);
    }

    public function testPositiveIdRejectsInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PositiveId::fromString('0');
    }

    public function testOrderDateRejectsInvalidCalendarDate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrderDate::fromString('2026-02-30');
    }

    public function testOrderStatusRejectsUnknownStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OrderStatus::fromString('cancelled');
    }

    public function testPriceAcceptsMinimalUnits(): void
    {
        self::assertSame(12345, Price::fromInt(12345)->value);
    }

    public function testPriceRejectsNegativeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Price::fromInt(-1);
    }
}
