<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Integration\Http;

use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseOwnerId;
use App\Tests\Modules\Finance\Expense\Integration\TestUtils\ExpenseService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class FetchAllExpensesTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = '/api/finance/expense';
    private const EXPENSE_AMOUNT = '100.20';
    private const EXPENSE_CURRENCY_CODE = 'PLN';
    private const PER_PAGE_LIMIT = 1;
    private ExpenseService $expenseService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        /** @var ExpenseService $expenseService */
        $expenseService = $this->container->get(ExpenseService::class);
        $this->expenseService = $expenseService;
    }

    /** @test */
    public function shouldNotFetchExpensesWithUnauthorizedUser(): void
    {
        // act
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_URL,
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function shouldReturnExpenses(): void
    {
        // arrange
        $expenseCategory = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        );
        $expense = $this->expenseService->storeExpense(
            ExpenseOwnerId::fromString($this->userId->toString()),
            $expenseCategory->getId(),
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE
        );

        // act
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader()
        );

        // assert
        /** @var string $responseContent */
        $responseContent = $this->client->getResponse()->getContent();
        /** @var object{items: array{object{id: string, categoryName: string, amount: string, currencyCode: string, comment: string|null, createdAt: string}}} $parsedResponse */
        $parsedResponse = json_decode($responseContent, false, 512, JSON_THROW_ON_ERROR);

        /** @var object{id: string, categoryName: string, amount: string, currencyCode: string, comment: string|null, createdAt: string} $fetchedExpense */
        $fetchedExpense = $parsedResponse->items[0];
        self::assertSame($expense->getId()->toString(), $fetchedExpense->id);
        self::assertSame($expenseCategory->getName(), $fetchedExpense->categoryName);
        self::assertSame(self::EXPENSE_AMOUNT, $fetchedExpense->amount);
        self::assertSame(self::EXPENSE_CURRENCY_CODE, $fetchedExpense->currencyCode);
    }

    /** @test */
    public function shouldReturnExpensesAccordingPaginationAndDescOrder(): void
    {
        // arrange
        $expenseCategory = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        );
        $expense = $this->expenseService->storeExpense(
            ExpenseOwnerId::fromString($this->userId->toString()),
            $expenseCategory->getId(),
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE
        );
        $secondExpense = $this->expenseService->storeExpense(
            ExpenseOwnerId::fromString($this->userId->toString()),
            $expenseCategory->getId(),
            self::EXPENSE_AMOUNT,
            self::EXPENSE_CURRENCY_CODE
        );

        // act
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_URL . '?perPage=' . self::PER_PAGE_LIMIT,
            server: $this->getAuthHeader()
        );

        /** @var string $responseContent */
        $responseContent = $this->client->getResponse()->getContent();

        // assert
        /** @var object{items: array{object{id: string, categoryName: string, amount: string, currencyCode: string, comment: string|null, createdAt: string}}} $parsedResponse */
        $parsedResponse = json_decode($responseContent, false, 512, JSON_THROW_ON_ERROR);
        /** @var object{id: string, categoryName: string, amount: string, currencyCode: string, comment: string|null, createdAt: string} $expense */
        $expenses = $parsedResponse->items;

        self::assertSame($secondExpense->getId()->toString(), $expenses[0]->id);
        self::assertCount(self::PER_PAGE_LIMIT, $expenses);
    }
}
