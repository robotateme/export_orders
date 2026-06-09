<?php

declare(strict_types=1);

namespace ExportOrders\Application\Command;

use ExportOrders\Application\Dto\ImportResult;
use ExportOrders\Application\Mapper\OrderInputMapper;
use ExportOrders\Application\Port\ClientRepository;
use ExportOrders\Application\Port\InvalidRowWriter;
use ExportOrders\Application\Port\MerchandiseRepository;
use ExportOrders\Application\Port\OrderRowReader;
use ExportOrders\Application\Port\OrderRepository;
use PDO;
use Throwable;

final class ImportOrdersHandler
{
    private OrderRowReader $rowReader;
    private InvalidRowWriter $invalidRowWriter;
    private ClientRepository $clients;
    private MerchandiseRepository $merchandise;
    private OrderRepository $orders;
    private PDO $transaction;
    private OrderInputMapper $orderInputMapper;

    public function __construct(
        OrderRowReader $rowReader,
        InvalidRowWriter $invalidRowWriter,
        ClientRepository $clients,
        MerchandiseRepository $merchandise,
        OrderRepository $orders,
        PDO $transaction,
        ?OrderInputMapper $orderInputMapper = null
    ) {
        $this->rowReader = $rowReader;
        $this->invalidRowWriter = $invalidRowWriter;
        $this->clients = $clients;
        $this->merchandise = $merchandise;
        $this->orders = $orders;
        $this->transaction = $transaction;
        $this->orderInputMapper = $orderInputMapper ?? new OrderInputMapper();
    }

    public function handle(ImportOrdersCommand $command): ImportResult
    {
        $processed = 0;
        $imported = 0;
        $invalid = 0;

        $this->transaction->beginTransaction();

        try {
            $this->invalidRowWriter->open($command->invalidPath);

            foreach ($this->rowReader->read($command->inputPath) as $row) {
                $processed++;
                $itemId = $this->orderInputMapper->itemId($row->fields);
                $price = $itemId === null ? null : $this->merchandise->priceById($itemId);

                if ($price === null) {
                    $this->invalidRowWriter->write($row->rawLine);
                    $invalid++;
                    continue;
                }

                $order = $this->orderInputMapper->toEntity(
                    $row->fields,
                    $command->status,
                    $command->orderDate,
                    $price
                );

                if (
                    $order === null
                    || !$this->clients->exists($order->customerId)
                ) {
                    $this->invalidRowWriter->write($row->rawLine);
                    $invalid++;
                    continue;
                }

                $this->orders->add($order);
                $imported++;
            }

            $this->transaction->commit();
        } catch (Throwable $e) {
            if ($this->transaction->inTransaction()) {
                $this->transaction->rollBack();
            }

            throw $e;
        } finally {
            $this->invalidRowWriter->close();
        }

        return new ImportResult($processed, $imported, $invalid);
    }
}
