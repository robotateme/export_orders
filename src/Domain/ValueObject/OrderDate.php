<?php

declare(strict_types=1);

namespace ExportOrders\Domain\ValueObject;

use DateTimeImmutable;
use Webmozart\Assert\Assert;

final class OrderDate
{
    public readonly string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        Assert::regex($value, '/^\d{4}-\d{2}-\d{2}$/', 'Order date must be a valid YYYY-MM-DD date.');

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        Assert::true(
            $date !== false && $date->format('Y-m-d') === $value,
            'Order date must be a valid YYYY-MM-DD date.'
        );

        return new self($value);
    }
}
