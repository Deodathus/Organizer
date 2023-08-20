<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Infrastructure\Repository;

use App\Modules\Finance\Wallet\Application\Exception\RequesterDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\ReadModel\TransactionReadModel as TransactionReadModelInterface;
use App\Modules\Finance\Wallet\Application\ViewModel\TransactionViewModel;
use App\Modules\Finance\Wallet\Domain\Exception\WalletDoesNotExist;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Application\Result\PaginatedResult;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class TransactionReadModel implements TransactionReadModelInterface
{
    public function __construct(
        private WalletRepository $walletRepository,
        private TransactionRepository $transactionRepository
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
            $transactions = $this->transactionRepository->fetchTransactionsByWallet(
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
}
