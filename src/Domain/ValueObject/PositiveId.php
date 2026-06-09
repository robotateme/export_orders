<?php

declare(strict_types=1);

namespace ExportOrders\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class PositiveId
{
    public readonly int $value;

    private function __construct(int $value)
    {
        Assert::positiveInteger($value, 'ID must be a positive integer.');
        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        Assert::regex($value, '/^[1-9][0-9]*$/', 'ID must be a positive integer.');

        return new self((int) $value);
    }
}
