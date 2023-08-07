<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\CommandHandler;

use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\Service\TransactionRegistrar;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Command\RegisterTransaction;
use App\Shared\Application\Messenger\CommandHandler;
use Money\Money;

final readonly class RegisterTransactionHandler implements CommandHandler
{
    public function __construct(
        private TransactionRegistrar $transactionRegistrar
    ) {}

    public function __invoke(RegisterTransaction $registerTransactionCommand): void
    {
        $this->transactionRegistrar->register(
            TransactionType::from($registerTransactionCommand->type->value),
            $registerTransactionCommand->walletId,
            new TransactionAmount($registerTransactionCommand->amount->value),
            new TransactionCreator($registerTransactionCommand->externalId),
        );
    }
}
