<?php

declare(strict_types=1);

namespace ExportOrders\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class OrderStatus
{
    private const ALLOWED = ['new', 'complete'];

    public readonly string $value;

    private function __construct(string $value)
    {
        Assert::oneOf($value, self::ALLOWED, 'Order status must be one of: new, complete.');
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self(trim($value));
    }
}
