<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\CommandHandler;

use App\Modules\Finance\Wallet\Application\Command\RegisterTransaction;
use App\Modules\Finance\Wallet\Application\Service\TransactionAmountCreator;
use App\Modules\Finance\Wallet\Application\Service\TransactionRegistrar;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Shared\Application\Messenger\CommandHandler;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class RegisterTransactionHandler implements CommandHandler
{
    public function __construct(
        private TransactionRegistrar $transactionRegistrar,
        private TransactionAmountCreator $transactionAmountCreator
    ) {}

    public function __invoke(RegisterTransaction $registerTransactionCommand): void
    {
        $this->transactionRegistrar->register(
            TransactionType::from($registerTransactionCommand->type->value),
            WalletId::fromString($registerTransactionCommand->walletId),
            $this->transactionAmountCreator->create(
                $registerTransactionCommand->amount->value,
                $registerTransactionCommand->amount->currencyCode
            ),
            $registerTransactionCommand->transactionCreator,
            $registerTransactionCommand->externalId,
            WalletId::fromString($registerTransactionCommand->walletId)
        );
    }
}
