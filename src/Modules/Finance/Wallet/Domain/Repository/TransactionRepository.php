<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Shared\Domain\ValueObject\WalletId;

interface TransactionRepository
{
    public function store(Transaction $transaction): void;

    /**
     * @return Transaction[]
     */
    public function fetchTransactionsByWallet(WalletId $walletId, WalletCurrency $walletCurrency): array;
}
