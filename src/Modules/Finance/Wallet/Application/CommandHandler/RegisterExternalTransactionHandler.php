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

final readonly class RegisterExternalTransactionHandler implements CommandHandler
{
    public function __construct(
        private CommandBus $commandBus
    ) {}

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
        } catch (WalletDoesNotExistException $exception) {
            throw CannotRegisterTransactionWithNonExistingWalletException::withPrevious($exception);
        } catch (CurrencyCodeIsNotSupportedException $exception) {
            throw CannotRegisterTransactionWithInvalidCurrencyCodeException::withPrevious($exception);
        } catch (CurrencyDoesNotExistException $exception) {
            throw CannotRegisterTransactionWithNonExistingCurrencyException::withPrevious($exception);
        } catch (TransactionCreatorDoesNotOwnWalletException $exception) {
            throw CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException::withPrevious($exception);
        } catch (TransactionCurrencyIsDifferentWalletHasException $exception) {
            throw CannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHasException::withPrevious(
                $exception
            );
        } catch (WalletBalanceIsNotEnoughToProceedTransactionException $exception) {
            throw CannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedException::withPrevious($exception);
        } catch (TransactionCreatorDoesNotExistException $exception) {
            throw CannotRegisterTransactionWithNonExistingTransactionCreatorException::withPrevious($exception);
        }
    }
}
