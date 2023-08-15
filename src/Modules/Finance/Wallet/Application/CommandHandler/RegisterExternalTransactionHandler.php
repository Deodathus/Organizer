<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\CommandHandler;

use App\Modules\Finance\Wallet\Application\Command\RegisterTransaction as RegisterTransactionApplicationCommand;
use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\DTO\TransactionAmount;
use App\Modules\Finance\Wallet\Application\DTO\TransactionType;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyCodeIsNotSupportedException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Wallet\Application\Exception\WalletBalanceIsNotEnoughToProceedTransactionException;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Command\RegisterTransaction;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithInvalidCurrencyCodeException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingCurrencyException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingTransactionCreatorException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingWalletException;
use App\Shared\Application\Messenger\CommandBus;
use App\Shared\Application\Messenger\CommandHandler;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final readonly class RegisterExternalTransactionHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(RegisterTransaction $registerTransactionCommand): void
    {
        try {
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
        } catch (HandlerFailedException $exception) {
            if ($exception->getPrevious() instanceof WalletDoesNotExistException) {
                throw CannotRegisterTransactionWithNonExistingWalletException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CurrencyCodeIsNotSupportedException) {
                throw CannotRegisterTransactionWithInvalidCurrencyCodeException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CurrencyDoesNotExistException) {
                throw CannotRegisterTransactionWithNonExistingCurrencyException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof TransactionCreatorDoesNotOwnWalletException) {
                throw CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof TransactionCurrencyIsDifferentWalletHasException) {
                throw CannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHasException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof WalletBalanceIsNotEnoughToProceedTransactionException) {
                throw CannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof TransactionCreatorDoesNotExistException) {
                throw CannotRegisterTransactionWithNonExistingTransactionCreatorException::withPrevious(
                    $exception->getPrevious()
                );
            }

            throw $exception;
        }
    }
}
