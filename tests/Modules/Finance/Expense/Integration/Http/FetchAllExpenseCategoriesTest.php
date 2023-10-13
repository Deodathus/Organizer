<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Integration\Http;

use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Tests\Modules\Finance\Expense\Integration\TestUtils\ExpenseService;
use App\Tests\Modules\Finance\Expense\TestUtils\ExpenseCategoryMother;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class FetchAllExpenseCategoriesTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = '/api/finance/expense/category';
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
    public function shouldNotFetchExpenseCategoriesWithUnauthorizedUser(): void
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
    public function shouldFetchAllCategories(): void
    {
        // arrange
        $category = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        );

        // act
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
        );

        // assert
        /** @var string $response */
        $response = $this->client->getResponse()->getContent();
        /** @var object{items: array{object{id: string, name: string}}} $parsedResponse */
        $parsedResponse = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

        self::assertTrue(isset($parsedResponse->items->{$category->getId()->toString()}));

        /** @var object{id: string, name: string} $fetchedCategory */
        $fetchedCategory = $parsedResponse->items->{$category->getId()->toString()};

        self::assertSame($category->getId()->toString(), $fetchedCategory->id);
        self::assertSame(ExpenseCategoryMother::CATEGORY_NAME, $fetchedCategory->name);
    }
}
