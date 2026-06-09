<?php

declare(strict_types=1);

namespace ExportOrders\Application\Port;

use ExportOrders\Domain\ValueObject\PositiveId;
use ExportOrders\Domain\ValueObject\Price;

interface MerchandiseRepository
{
    public function priceById(PositiveId $id): ?Price;
}
