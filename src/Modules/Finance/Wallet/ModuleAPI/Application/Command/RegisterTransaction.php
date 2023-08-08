<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\Command;

use App\Modules\Finance\Wallet\Application\CommandHandler\RegisterExternalTransactionHandler;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\SupportedTransactionType;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\TransactionAmount;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\TransactionCreator;
use App\Shared\Application\Messenger\Command;

/** @see RegisterExternalTransactionHandler */
final readonly class RegisterTransaction implements Command
{
    public function __construct(
        public SupportedTransactionType $type,
        public string $walletId,
        public TransactionAmount $amount,
        public TransactionCreator $transactionCreator,
        public string $externalId
    ) {}
}
