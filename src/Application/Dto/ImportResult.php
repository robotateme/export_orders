<?php

declare(strict_types=1);

namespace ExportOrders\Application\Dto;

final class ImportResult
{
    public readonly int $processed;
    public readonly int $imported;
    public readonly int $invalid;

    public function __construct(
        int $processed,
        int $imported,
        int $invalid
    ) {
        $this->processed = $processed;
        $this->imported = $imported;
        $this->invalid = $invalid;
    }
}
