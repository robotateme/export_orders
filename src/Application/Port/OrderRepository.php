<?php

declare(strict_types=1);

namespace ExportOrders\Application\Port;

use ExportOrders\Domain\Entity\Order;

interface OrderRepository
{
    public function add(Order $order): void;
}
