<?php

declare(strict_types=1);

namespace App\Modules\Finance\Expense\Application\CommandHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Expense\Application\Command\StoreExpense;
use App\Modules\Finance\Expense\Application\DTO\CreatedExpense;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseExpenseCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseToNonExistingWalletException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseWithInvalidCurrencyCodeException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseWithNonExistingCurrencyException;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCategoryDoesNotExistException;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCreatorDoesNotExistException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException;
use App\Modules\Finance\Expense\Domain\Entity\Expense;
use App\Modules\Finance\Expense\Domain\Exception\ExpenseCategoryDoesNotExist;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseCategoryRepository;
use App\Modules\Finance\Expense\Domain\Repository\ExpenseRepository;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseAmount;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Command\RegisterTransaction;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\SupportedTransactionType;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\TransactionAmount;
use App\Modules\Finance\Wallet\ModuleAPI\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithInvalidCurrencyCodeException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingCurrencyException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingTransactionCreatorException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingWalletException;
use App\Shared\Application\Messenger\CommandBus;
use App\Shared\Application\Messenger\CommandHandler;
use App\Shared\Application\Messenger\QueryBus;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final readonly class StoreExpenseHandler implements CommandHandler
{
    public function __construct(
        private ExpenseRepository $expenseRepository,
        private ExpenseCategoryRepository $expenseCategoryRepository,
        private CommandBus $commandBus,
        private QueryBus $queryBus
    ) {
    }

    /**
     * @throws CannotRegisterExpenseBecauseCreatorDoesNotOwnWalletException
     * @throws CannotRegisterExpenseBecauseExpenseCurrencyIsDifferentWalletHasException
     * @throws CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException
     * @throws CannotRegisterExpenseToNonExistingWalletException
     * @throws CannotRegisterExpenseWithInvalidCurrencyCodeException
     * @throws CannotRegisterExpenseWithNonExistingCurrencyException
     * @throws ExpenseCategoryDoesNotExistException
     * @throws ExpenseCreatorDoesNotExistException
     */
    public function __invoke(StoreExpense $storeExpenseCommand): CreatedExpense
    {
        try {
            /** @var UserDTO $creator */
            $creator = $this->queryBus->handle(new FetchUserIdByToken($storeExpenseCommand->ownerApiToken));
        } catch (UserDoesNotExist $exception) {
            throw ExpenseCreatorDoesNotExistException::withToken($storeExpenseCommand->ownerApiToken);
        }

        try {
            $category = $this->expenseCategoryRepository->fetchById(
                ExpenseCategoryId::fromString($storeExpenseCommand->categoryId)
            );
        } catch (ExpenseCategoryDoesNotExist $exception) {
            throw ExpenseCategoryDoesNotExistException::fromPrevious($exception);
        }

        $expense = Expense::create(
            ExpenseOwnerId::fromString($creator->userId),
            $category->getId(),
            new ExpenseAmount($storeExpenseCommand->amount, $storeExpenseCommand->currencyCode),
            $storeExpenseCommand->comment
        );

        $this->registerTransaction($storeExpenseCommand, $expense);

        $this->expenseRepository->store($expense);

        return new CreatedExpense($expense->getId()->toString());
    }

    private function registerTransaction(StoreExpense $storeExpenseCommand, Expense $expense): void
    {
        try {
            $this->commandBus->dispatch(
                new RegisterTransaction(
                    SupportedTransactionType::EXPENSE,
                    $storeExpenseCommand->walletId,
                    new TransactionAmount($storeExpenseCommand->amount, $storeExpenseCommand->currencyCode),
                    new TransactionCreator($storeExpenseCommand->ownerApiToken),
                    $expense->getId()->toString()
                )
            );
        } catch (HandlerFailedException $exception) {
            if ($exception->getPrevious() instanceof CannotRegisterTransactionWithNonExistingWalletException) {
                throw CannotRegisterExpenseToNonExistingWalletException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CannotRegisterTransactionWithNonExistingCurrencyException) {
                throw CannotRegisterExpenseWithNonExistingCurrencyException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CannotRegisterTransactionWithInvalidCurrencyCodeException) {
                throw CannotRegisterExpenseWithInvalidCurrencyCodeException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException) {
                throw CannotRegisterExpenseBecauseCreatorDoesNotOwnWalletException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHasException) {
                throw CannotRegisterExpenseBecauseExpenseCurrencyIsDifferentWalletHasException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedException) {
                throw CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException::withPrevious(
                    $exception->getPrevious()
                );
            }
            if ($exception->getPrevious() instanceof CannotRegisterTransactionWithNonExistingTransactionCreatorException) {
                throw ExpenseCreatorDoesNotExistException::withToken($storeExpenseCommand->ownerApiToken);
            }

            throw $exception;
        }
    }
}
