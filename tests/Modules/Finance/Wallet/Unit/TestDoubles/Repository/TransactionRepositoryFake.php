<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Shared\Domain\ValueObject\WalletId;

final class TransactionRepositoryFake implements TransactionRepository
{
    /** @var Transaction[] $transactions */
    private array $transactions = [];

    public function store(Transaction $transaction): void
    {
        $this->transactions[$transaction->getId()->toString()] = $transaction;
    }

    public function fetchTransactionsByWallet(
        WalletId $walletId,
        WalletCurrency $walletCurrency,
        ?int $perPage = null,
        ?int $page = null
    ): array {
        $result = [];

        foreach ($this->transactions as $transaction) {
            if ($transaction->getWalletId()->toString() === $walletId->toString()) {
                $result[] = $transaction;
            }
        }

        return $result;
    }
}
