<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Application\Service\TransactionAmountCreator;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository as TransactionRepositoryInterface;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Shared\Domain\ValueObject\WalletId;
use Doctrine\DBAL\Connection;

final readonly class TransactionRepository implements TransactionRepositoryInterface
{
    private const DB_TABLE_NAME = 'transactions';

    public function __construct(
        private Connection $connection,
        private TransactionAmountCreator $transactionAmountCreator
    ) {}

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

    public function fetchTransactionsByWallet(WalletId $walletId, WalletCurrency $walletCurrency): array
    {
        $result = [];
        $rawData = $this->connection->createQueryBuilder()
            ->select('id', 'external_id', 'creator_id', 'amount', 'type')
            ->from(self::DB_TABLE_NAME)
            ->where('wallet_id = :walletId')
            ->orderBy('created_at')
            ->setParameter('walletId', $walletId->toString())
            ->fetchAllAssociative();

        foreach ($rawData as $singleTransactionData) {
            $transactionExternalId = null;
            if ($singleTransactionData['external_id'] !== null) {
                $transactionExternalId = TransactionExternalId::fromString($singleTransactionData['externalId']);
            }

            $result[] = Transaction::reproduce(
                TransactionId::fromString($singleTransactionData['id']),
                $walletId,
                $this->transactionAmountCreator->create($singleTransactionData['amount'], $walletCurrency->currencyCode),
                TransactionType::from($singleTransactionData['type']),
                TransactionCreator::fromString($singleTransactionData['creator_id']),
                $transactionExternalId
            );
        }

        return $result;
    }
}
