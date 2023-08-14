<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Integration\Http;

use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryId;
use App\Tests\Modules\Finance\Expense\Integration\TestUtils\ExpenseService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class StoreExpenseCategoryTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = '/api/finance/expense/category';
    private const TEST_CATEGORY_NAME = 'Test';
    private ExpenseService $expenseService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        $this->expenseService = $this->container->get(ExpenseService::class);
    }

    /** @test */
    public function shouldNotStoreExpenseCategoryWithUnauthorizedUser(): void
    {
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            content: json_encode([
                'name' => self::TEST_CATEGORY_NAME,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function shouldStoreExpenseCategory(): void
    {
        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode([
                'name' => self::TEST_CATEGORY_NAME,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $createdCategoryId = json_decode($this->client->getResponse()->getContent())->id;
        $createdCategory = $this->expenseService->fetchCategoryById(
            ExpenseCategoryId::fromString($createdCategoryId)
        );

        self::assertNotNull($createdCategory);
        self::assertSame(self::TEST_CATEGORY_NAME, $createdCategory->getName());
        self::assertSame($this->userId->toString(), $createdCategory->getCategoryOwnerId()->toString());
    }
}
