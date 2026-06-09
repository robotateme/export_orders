<?php

declare(strict_types=1);

namespace ExportOrders\Application\Port;

use ExportOrders\Domain\ValueObject\PositiveId;

interface ClientRepository
{
    public function exists(PositiveId $id): bool;
}
