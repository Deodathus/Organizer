<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Application\Exception\RequesterDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\ReadModel\TransactionReadModel as TransactionReadModelInterface;
use App\Modules\Finance\Wallet\Application\Service\TransactionAmountResolver;
use App\Modules\Finance\Wallet\Application\ViewModel\TransactionViewModel;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Exception\WalletDoesNotExist;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Application\Result\PaginatedResult;
use App\Shared\Domain\ValueObject\WalletId;
use Doctrine\DBAL\Connection;

final readonly class TransactionReadModel implements TransactionReadModelInterface
{
    private const DB_TABLE_NAME = 'transactions';

    public function __construct(
        private WalletRepository $walletRepository,
        private TransactionAmountResolver $transactionAmountResolver,
        private TransactionRepository $transactionRepository,
        private Connection $connection
    ) {
    }

    /**
     * @throws RequesterDoesNotOwnWalletException
     * @throws WalletDoesNotExistException
     */
    public function fetchByWallet(
        WalletOwnerExternalId $ownerId,
        WalletId $walletId,
        int $perPage,
        int $page
    ): PaginatedResult {
        if (!$this->walletRepository->walletExists($walletId)) {
            throw WalletDoesNotExistException::withId($walletId->toString());
        }

        if (!$this->walletRepository->doesWalletBelongToOwner($walletId, $ownerId)) {
            throw RequesterDoesNotOwnWalletException::withRequesterIdAndWalletId($ownerId->toString(), $walletId->toString());
        }

        try {
            $transactions = $this->fetchTransactions(
                $walletId,
                $this->walletRepository->fetchWalletCurrency($walletId),
                $perPage,
                $page
            );
        } catch (WalletDoesNotExist $exception) {
            throw WalletDoesNotExistException::withIdAndRequesterId($walletId->toString(), $ownerId->toString());
        }

        $result = [];

        foreach ($transactions as $transaction) {
            $result[] = TransactionViewModel::fromEntity($transaction);
        }

        return new PaginatedResult(
            $result,
            $this->transactionRepository->fetchTransactionsCountByWallet($walletId)
        );
    }

    /**
     * @return Transaction[]
     */
    private function fetchTransactions(
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
            ->orderBy('created_at', 'DESC')
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
                new \DateTimeImmutable($singleTransactionData['created_at']),
                $transactionExternalId
            );
        }

        return $result;
    }
}
