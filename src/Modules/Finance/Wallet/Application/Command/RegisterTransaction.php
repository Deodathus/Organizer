<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Command;

use App\Modules\Finance\Wallet\Application\CommandHandler\RegisterTransactionHandler;
use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\DTO\TransactionAmount;
use App\Modules\Finance\Wallet\Application\DTO\TransactionType;
use App\Shared\Application\Messenger\Command;

/** @see RegisterTransactionHandler */
final readonly class RegisterTransaction implements Command
{
    public function __construct(
        public TransactionType $type,
        public string $walletId,
        public TransactionAmount $amount,
        public TransactionCreator $transactionCreator,
        public ?string $externalId = null,
        public ?string $receiverWalletId = null
    ) {}
}
