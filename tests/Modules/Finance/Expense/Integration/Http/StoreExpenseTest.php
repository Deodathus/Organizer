<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Expense\Integration\Http;

use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseCategoryOwnerId;
use App\Modules\Finance\Expense\Domain\ValueObject\ExpenseId;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Tests\Modules\Finance\Expense\Integration\TestUtils\ExpenseService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group integration
 */
final class StoreExpenseTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = 'api/finance/expense';
    private ExpenseService $expenseService;
    private const EXPENSE_AMOUNT = '100';
    private const EXPENSE_DECIMAL_AMOUNT = '99.50';
    private const BIG_EXPENSE_AMOUNT = '500';
    private const BIG_EXPENSE_DECIMAL_AMOUNT = '500.11';
    private const EXPENSE_CURRENCY_CODE = SupportedCurrencies::PLN->value;
    private const OTHER_CURRENCY_CODE = SupportedCurrencies::USD->value;
    private const INVALID_CURRENCY_CODE = 'xxx';
    private const EXPENSE_COMMENT = 'Test expense';

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        /** @var ExpenseService $expenseService */
        $expenseService = $this->container->get(ExpenseService::class);
        $this->expenseService = $expenseService;
    }

    /** @test */
    public function shouldNotStoreExpenseWithUnauthorizedUser(): void
    {
        // arrange
        $walletId = Uuid::uuid4();
        $categoryId = Uuid::uuid4();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => self::EXPENSE_AMOUNT,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /**
     * @dataProvider expenseAmountsDataProvider
     *
     * @test
     */
    public function shouldStoreExpense(string $expenseAmount): void
    {
        // arrange
        $currency = $this->expenseService->storeCurrency(SupportedCurrencies::from(self::EXPENSE_CURRENCY_CODE));
        $walletId = $this->expenseService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::EXPENSE_CURRENCY_CODE
            )
        )->getId()->toString();
        $categoryId = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        )->getId()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => $expenseAmount,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();

        /** @var string $responseContent */
        $responseContent = $response->getContent();
        /** @var object{id: string} $parsedResponse */
        $parsedResponse = json_decode(
            $responseContent,
            false,
            512,
            JSON_THROW_ON_ERROR
        );
        $createdExpenseId = $parsedResponse->id;
        $createdExpense = $this->expenseService->fetchExpenseById(ExpenseId::fromString($createdExpenseId));

        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertSame($expenseAmount, $createdExpense->getAmount()->amount);
        self::assertSame(self::EXPENSE_CURRENCY_CODE, $createdExpense->getAmount()->currencyCode);
        self::assertSame(self::EXPENSE_COMMENT, $createdExpense->getComment());
        self::assertSame($this->userId->toString(), $createdExpense->getOwnerId()->toString());
        self::assertSame($categoryId, $createdExpense->getCategoryId()->toString());
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseWalletDoesNotExist(): void
    {
        // arrange
        $walletId = Uuid::uuid4()->toString();
        $categoryId = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        )->getId()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => self::EXPENSE_AMOUNT,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCurrencyDoesNotExist(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $walletId = $this->expenseService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::EXPENSE_CURRENCY_CODE
            )
        )->getId()->toString();
        $categoryId = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        )->getId()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => self::EXPENSE_AMOUNT,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseCurrencyCodeIsInvalid(): void
    {
        // arrange
        $currency = $this->expenseService->storeCurrency(SupportedCurrencies::from(self::EXPENSE_CURRENCY_CODE));
        $walletId = $this->expenseService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::EXPENSE_CURRENCY_CODE
            )
        )->getId()->toString();
        $categoryId = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        )->getId()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => self::EXPENSE_AMOUNT,
                    'currencyCode' => self::INVALID_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseExpenseCreatorDoesNotOwnWallet(): void
    {
        // arrange
        $currency = $this->expenseService->storeCurrency(SupportedCurrencies::from(self::EXPENSE_CURRENCY_CODE));
        $nonExistingUserId = Uuid::uuid4()->toString();
        $walletId = $this->expenseService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($nonExistingUserId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::EXPENSE_CURRENCY_CODE
            )
        )->getId()->toString();
        $categoryId = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        )->getId()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => self::EXPENSE_AMOUNT,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseExpenseCurrencyIsDifferentWalletHas(): void
    {
        // arrange
        $currency = $this->expenseService->storeCurrency(SupportedCurrencies::from(self::EXPENSE_CURRENCY_CODE));
        $walletId = $this->expenseService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::OTHER_CURRENCY_CODE
            )
        )->getId()->toString();
        $categoryId = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        )->getId()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => self::EXPENSE_AMOUNT,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @dataProvider bigExpenseAmountsDataProvider
     *
     * @test
     */
    public function shouldNotStoreExpenseBecauseWalletBalanceIsNotEnoughToProceedExpense(string $expenseAmount): void
    {
        // arrange
        $currency = $this->expenseService->storeCurrency(SupportedCurrencies::from(self::EXPENSE_CURRENCY_CODE));
        $walletId = $this->expenseService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::EXPENSE_CURRENCY_CODE
            )
        )->getId()->toString();
        $categoryId = $this->expenseService->storeExpenseCategory(
            ExpenseCategoryOwnerId::fromString($this->userId->toString())
        )->getId()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => $expenseAmount,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /** @test */
    public function shouldNotStoreExpenseBecauseExpenseCategoryDoesNotExist(): void
    {
        // arrange
        $currency = $this->expenseService->storeCurrency(SupportedCurrencies::from(self::EXPENSE_CURRENCY_CODE));
        $walletId = $this->expenseService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::EXPENSE_CURRENCY_CODE
            )
        )->getId()->toString();
        $categoryId = Uuid::uuid4()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader(),
            content: json_encode(
                [
                    'walletId' => $walletId,
                    'categoryId' => $categoryId,
                    'amount' => self::EXPENSE_AMOUNT,
                    'currencyCode' => self::EXPENSE_CURRENCY_CODE,
                    'comment' => self::EXPENSE_COMMENT,
                ],
                JSON_THROW_ON_ERROR
            )
        );

        // assert
        $response = $this->client->getResponse();
        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @return array<array<string>>
     */
    public function expenseAmountsDataProvider(): array
    {
        return [
            [
                self::EXPENSE_AMOUNT,
            ],
            [
                self::EXPENSE_DECIMAL_AMOUNT,
            ],
        ];
    }

    /**
     * @return array<array<string>>
     */
    public function bigExpenseAmountsDataProvider(): array
    {
        return [
            [
                self::BIG_EXPENSE_AMOUNT,
            ],
            [
                self::BIG_EXPENSE_DECIMAL_AMOUNT,
            ],
        ];
    }
}
