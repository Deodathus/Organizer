<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Shared\Domain\ValueObject\WalletId;

interface TransactionRepository
{
    public function store(Transaction $transaction): void;

    /**
     * @return Transaction[]
     */
    public function fetchTransactionsByWallet(
        WalletId $walletId,
        WalletCurrency $walletCurrency,
        ?int $perPage = null,
        ?int $page = null
    ): array;

    public function fetchTransactionsCountByWallet(WalletId $walletId): int;

    /**
     * @return array<TransactionId>
     */
    public function fetchTransactionsIdsByOwnerAndMonth(TransactionCreator $transactionCreator, int $month): array;
}
