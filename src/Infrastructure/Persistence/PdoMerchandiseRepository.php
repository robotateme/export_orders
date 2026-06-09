<?php

declare(strict_types=1);

namespace ExportOrders\Infrastructure\Persistence;

use ExportOrders\Application\Port\MerchandiseRepository;
use ExportOrders\Domain\ValueObject\PositiveId;
use ExportOrders\Domain\ValueObject\Price;
use PDO;
use PDOStatement;

final class PdoMerchandiseRepository implements MerchandiseRepository
{
    private PDOStatement $priceById;

    public function __construct(PDO $pdo, string $priceByIdSql)
    {
        $this->priceById = $pdo->prepare($priceByIdSql);
    }

    public function priceById(PositiveId $id): ?Price
    {
        $this->priceById->execute([':id' => $id->value]);
        $price = $this->priceById->fetchColumn();

        if (!is_int($price) && !is_string($price)) {
            return null;
        }

        return Price::fromInt((int) $price);
    }
}
