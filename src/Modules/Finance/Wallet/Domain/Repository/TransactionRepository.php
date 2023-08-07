<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Repository;

use App\Modules\Finance\Wallet\Domain\Entity\Transaction;

interface TransactionRepository
{
    public function store(Transaction $transaction): void;
}
