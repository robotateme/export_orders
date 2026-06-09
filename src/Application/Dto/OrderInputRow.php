<?php

declare(strict_types=1);

namespace ExportOrders\Application\Dto;

final class OrderInputRow
{
    public readonly int $lineNumber;
    public readonly string $rawLine;
    /** @var list<string> */
    public readonly array $fields;

    /**
     * @param list<string> $fields
     */
    public function __construct(
        int $lineNumber,
        string $rawLine,
        array $fields
    ) {
        $this->lineNumber = $lineNumber;
        $this->rawLine = $rawLine;
        $this->fields = $fields;
    }
}
