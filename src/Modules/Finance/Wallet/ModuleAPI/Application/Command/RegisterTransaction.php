<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\ModuleAPI\Application\Command;

use App\Modules\Finance\Wallet\Application\CommandHandler\RegisterTransactionHandler;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\TransactionAmount;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Enum\SupportedTransactionType;
use App\Shared\Application\Messenger\Command;
use App\Shared\Domain\ValueObject\WalletId;

/** @see RegisterTransactionHandler */
final readonly class RegisterTransaction implements Command
{
    public function __construct(
        public string $externalId,
        public SupportedTransactionType $type,
        public WalletId $walletId,
        public TransactionAmount $amount,
        public TransactionCreator $transactionCreator
    ) {}
}
