<?php

declare(strict_types=1);

namespace ExportOrders\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class Price
{
    public readonly int $value;

    private function __construct(int $value)
    {
        Assert::greaterThanEq($value, 0, 'Price must be a non-negative integer in minimal units.');
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }
}
