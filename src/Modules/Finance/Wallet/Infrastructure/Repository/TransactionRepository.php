<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository as TransactionRepositoryInterface;
use Doctrine\DBAL\Connection;

final readonly class TransactionRepository implements TransactionRepositoryInterface
{
    private const DB_TABLE_NAME = 'transactions';

    public function __construct(
        private Connection $connection
    ) {}

    public function store(Transaction $transaction): void
    {
        $this->connection->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
            ->values([
                'id' => ':id',
                'external_id' => ':externalId',
                'wallet_id' => ':walletId',
                'amount' => ':amount',
                'type' => ':type',
            ])
            ->setParameters([
                'id' => $transaction->getId()->toString(),
                'externalId' => $transaction->getExternalId()?->toString(),
                'walletId' => $transaction->getWalletId()->toString(),
                'amount' => $transaction->getAmount()->toString(),
                'type' => $transaction->getType()->value,
            ])
            ->executeStatement();
    }
}
