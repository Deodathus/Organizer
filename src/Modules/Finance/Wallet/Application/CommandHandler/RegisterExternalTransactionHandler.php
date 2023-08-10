<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\CommandHandler;

use App\Modules\Finance\Wallet\Application\Command\RegisterTransaction as RegisterTransactionApplicationCommand;
use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\DTO\TransactionAmount;
use App\Modules\Finance\Wallet\Application\DTO\TransactionType;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Command\RegisterTransaction;
use App\Shared\Application\Messenger\CommandBus;
use App\Shared\Application\Messenger\CommandHandler;

final readonly class RegisterExternalTransactionHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus
    ) {}

    public function __invoke(RegisterTransaction $registerTransactionCommand): void
    {
        $this->commandBus->dispatch(
            new RegisterTransactionApplicationCommand(
                TransactionType::from($registerTransactionCommand->type->value),
                $registerTransactionCommand->walletId,
                new TransactionAmount(
                    $registerTransactionCommand->amount->value,
                    $registerTransactionCommand->amount->currencyCode
                ),
                new TransactionCreator($registerTransactionCommand->transactionCreator->externalId),
                $registerTransactionCommand->externalId
            )
        );
    }
}
