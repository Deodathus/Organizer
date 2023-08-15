<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Unit\Application\CommandHandler;

use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Expense\Application\Command\StoreExpense;
use App\Modules\Finance\Expense\Application\CommandHandler\StoreExpenseHandler;
use App\Modules\Finance\Expense\Application\DTO\CreatedExpense;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseExpenseCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseToNonExistingWalletException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseWithInvalidCurrencyCodeException;
use App\Modules\Finance\Expense\Application\Exception\CannotRegisterExpenseWithNonExistingCurrencyException;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCategoryDoesNotExistException;
use App\Modules\Finance\Expense\Application\Exception\ExpenseCreatorDoesNotExistException;
use App\Modules\Finance\Expense\Domain\Entity\ExpenseCategory;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Command\RegisterTransaction;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithInvalidCurrencyCodeException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingCurrencyException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingTransactionCreatorException;
use App\Modules\Finance\Wallet\ModuleAPI\Application\Exception\CannotRegisterTransactionWithNonExistingWalletException;
use App\Tests\Modules\Finance\Expense\Unit\TestDoubles\QueryHandler\FetchUserIdByTokenHandlerStub;
use App\Tests\Modules\Finance\Expense\Unit\TestDoubles\Repository\ExpenseCategoryRepositoryFake;
use App\Tests\Modules\Finance\Expense\Unit\TestDoubles\Repository\ExpenseRepositoryFake;
use App\Tests\SharedInfrastructure\Unit\Application\Messenger\CommandBusStub;
use App\Tests\SharedInfrastructure\Unit\Application\Messenger\QueryBusFake;
use App\Tests\SharedInfrastructure\Unit\Application\Messenger\ThrowingExceptionCommandBusStub;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class StoreExpenseTest extends TestCase
{
    private const EXPENSE_AMOUNT = '100';
    private const BIG_EXPENSE_AMOUNT = '500';
    private const EXPENSE_CURRENCY_CODE = SupportedCurrencies::PLN->value;
    private const OTHER_CURRENCY_CODE = SupportedCurrencies::USD->value;
    private const NON_EXISTING_CURRENCY_CODE = 'xxx';
    private const EXPENSE_COMMENT = 'Test expense';
    private const EXPENSE_CATEGORY = 'Test category';

    /** @test */
    public function shouldStoreExpense(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new CommandBusStub();
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));

        // assert
        $createdExpense = $expenseRepository->fetchById(ExpenseId::fromString($result->expenseId));

        self::assertNotNull($createdExpense);
        self::assertTrue($commandBus->wasHandled(RegisterTransaction::class));
        self::assertSame(self::EXPENSE_AMOUNT, $createdExpense->getAmount()->amount);
        self::assertSame(self::EXPENSE_CURRENCY_CODE, $createdExpense->getAmount()->currencyCode);
        self::assertSame(self::EXPENSE_COMMENT, $createdExpense->getComment());
        self::assertSame($userId, $createdExpense->getOwnerId()->toString());
        self::assertSame($expenseCategoryId, $createdExpense->getCategoryId()->toString());
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCreatorDoesNotExist(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new CommandBusStub();
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub());

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(ExpenseCreatorDoesNotExistException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseExpenseCategoryDoesNotExist(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new CommandBusStub();
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake();

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(ExpenseCategoryDoesNotExistException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseWalletDoesNotExist(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new ThrowingExceptionCommandBusStub(new CannotRegisterTransactionWithNonExistingWalletException());
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(CannotRegisterExpenseToNonExistingWalletException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCannotRegisterTransactionWithNonExistingCurrency(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new ThrowingExceptionCommandBusStub(
            new CannotRegisterTransactionWithNonExistingCurrencyException()
        );
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(CannotRegisterExpenseWithNonExistingCurrencyException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCannotRegisterTransactionWithInvalidCurrencyCode(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new ThrowingExceptionCommandBusStub(
            new CannotRegisterTransactionWithInvalidCurrencyCodeException()
        );
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(CannotRegisterExpenseWithInvalidCurrencyCodeException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWallet(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new ThrowingExceptionCommandBusStub(
            new CannotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWalletException()
        );
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(CannotRegisterExpenseBecauseCreatorDoesNotOwnWalletException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHas(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new ThrowingExceptionCommandBusStub(
            new CannotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHasException()
        );
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(CannotRegisterExpenseBecauseExpenseCurrencyIsDifferentWalletHasException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceed(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new ThrowingExceptionCommandBusStub(
            new CannotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedException()
        );
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(CannotRegisterExpenseBecauseWalletBalanceIsNotEnoughToProceedException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCannotRegisterTransactionWithNonExistingTransactionCreator(): void
    {
        // arrange
        $userToken = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $walletId = Uuid::uuid4()->toString();
        $expenseCategoryId = Uuid::uuid4()->toString();

        $commandBus = new ThrowingExceptionCommandBusStub(
            new CannotRegisterTransactionWithNonExistingTransactionCreatorException()
        );
        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $expenseRepository = new ExpenseRepositoryFake();
        $expenseCategoryRepository = new ExpenseCategoryRepositoryFake([
            $expenseCategoryId => ExpenseCategory::recreate(
                ExpenseCategoryId::fromString($expenseCategoryId),
                ExpenseCategoryOwnerId::fromString($userId),
                self::EXPENSE_CATEGORY
            ),
        ]);

        $sut = new StoreExpenseHandler(
            $expenseRepository,
            $expenseCategoryRepository,
            $commandBus,
            $queryBus
        );

        // assert
        $this->expectException(ExpenseCreatorDoesNotExistException::class);

        // act
        /** @var CreatedExpense $result */
        $result = ($sut)(new StoreExpense(
            $walletId,
            $userToken,
            $expenseCategoryId,
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE,
            self::EXPENSE_COMMENT
        ));
    }
}
