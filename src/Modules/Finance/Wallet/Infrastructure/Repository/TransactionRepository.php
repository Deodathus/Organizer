<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Application\Service\TransactionAmountResolver;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository as TransactionRepositoryInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Shared\Domain\ValueObject\WalletId;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

final readonly class TransactionRepository implements TransactionRepositoryInterface
{
    private const DB_TABLE_NAME = 'transactions';

    public function __construct(
        private Connection $connection,
        private TransactionAmountResolver $transactionAmountResolver
    ) {
    }

    public function store(Transaction $transaction): void
    {
        $this->connection->createQueryBuilder()
            ->insert(self::DB_TABLE_NAME)
            ->values([
                'id' => ':id',
                'external_id' => ':externalId',
                'creator_id' => ':creatorId',
                'wallet_id' => ':walletId',
                'amount' => ':amount',
                'type' => ':type',
            ])
            ->setParameters([
                'id' => $transaction->getId()->toString(),
                'externalId' => $transaction->getExternalId()?->toString(),
                'creatorId' => $transaction->getTransactionCreator()->toString(),
                'walletId' => $transaction->getWalletId()->toString(),
                'amount' => $transaction->getAmount()->toString(),
                'type' => $transaction->getType()->value,
            ])
            ->executeStatement();
    }

    public function fetchTransactionsByWallet(
        WalletId $walletId,
        WalletCurrency $walletCurrency,
        ?int $perPage = null,
        ?int $page = null
    ): array {
        $result = [];

        $transactionsQuery = $this->connection->createQueryBuilder()
            ->select('id', 'external_id', 'creator_id', 'amount', 'type', 'created_at')
            ->from(self::DB_TABLE_NAME)
            ->where('wallet_id = :walletId')
            ->orderBy('created_at')
            ->setParameter('walletId', $walletId->toString());

        if ($perPage) {
            $transactionsQuery->setMaxResults($perPage);
        }

        if ($page) {
            $transactionsQuery->setFirstResult($page * $perPage - $perPage);
        }

        /** @var array<int, array{id: string, external_id: string|null, creator_id: string, amount: string, type: string, created_at: string}> $rawData */
        $rawData = $transactionsQuery->fetchAllAssociative();

        foreach ($rawData as $singleTransactionData) {
            $transactionExternalId = null;
            if ($singleTransactionData['external_id'] !== null) {
                $transactionExternalId = TransactionExternalId::fromString($singleTransactionData['external_id']);
            }

            $result[] = Transaction::reproduce(
                TransactionId::fromString($singleTransactionData['id']),
                $walletId,
                $this->transactionAmountResolver->resolve($singleTransactionData['amount'], $walletCurrency->currencyCode),
                TransactionType::from($singleTransactionData['type']),
                TransactionCreator::fromString($singleTransactionData['creator_id']),
                new DateTimeImmutable($singleTransactionData['created_at']),
                $transactionExternalId
            );
        }

        return $result;
    }

    public function fetchTransactionsCountByWallet(WalletId $walletId): int
    {
        /** @var array{count: int}|false $rawResult */
        $rawResult = $this->connection->createQueryBuilder()
            ->select('count(id) as count')
            ->from(self::DB_TABLE_NAME)
            ->where('wallet_id = :walletId')
            ->setParameter('walletId', $walletId->toString())
            ->fetchAssociative();

        if (!$rawResult) {
            return 0;
        }

        return $rawResult['count'];
    }

    public function fetchTransactionsIdsByOwnerAndMonth(TransactionCreator $transactionCreator, int $month): array
    {
        $rawResult = $this->connection->createQueryBuilder()
            ->select('external_id')
            ->from(self::DB_TABLE_NAME)
            ->where('creator_id = :creatorId')
            ->setParameter('creatorId', $transactionCreator->toString())
            ->andWhere('MONTH(created_at) = :month')
            ->setParameter('month', $month)
            ->andWhere('external_id is not null')
            ->fetchAllAssociative();

        return array_map(static fn(array $transaction) => $transaction['external_id'], $rawResult);
    }
}
