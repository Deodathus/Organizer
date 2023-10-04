<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\Http;

use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Shared\Domain\ValueObject\WalletId;
use App\Tests\Modules\Finance\Wallet\Integration\TestUtils\WalletService;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Service\MoneyAmountNormalizer;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class StoreTransactionTest extends IntegrationTestBase
{
    private const API_URL = '/api/finance/wallet/%s/transaction';
    private const WALLET_CURRENCY = SupportedCurrencies::PLN;
    private const TRANSACTION_AMOUNT = '100';
    private const TRANSACTION_DECIMAL_AMOUNT = '99.79';
    private const BIG_TRANSACTION_AMOUNT = '500';
    private const BIG_TRANSACTION_DECIMAL_AMOUNT = '500.25';
    private const ANOTHER_CURRENCY = SupportedCurrencies::USD;
    private const UNSUPPORTED_CURRENCY_CODE = 'xxx';
    private const INVALID_TRANSACTION_TYPE = 'xxx';

    private WalletService $walletService;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        /** @var WalletService $walletService */
        $walletService = $this->container->get(WalletService::class);
        $this->walletService = $walletService;
    }

    /** @test */
    public function shouldNotStoreTransactionWithUnauthorizedUser(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $walletCurrency = new WalletCurrency(
            WalletCurrencyId::fromString($currency->getId()->toString()),
            self::WALLET_CURRENCY->value
        );
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            $walletCurrency
        );

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::WALLET_CURRENCY,
                'transactionType' => TransactionType::DEPOSIT,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet(
                $wallet->getId(),
                $walletCurrency
            )
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseCurrencyCodeIsNotSupported(): void
    {
        // arrange
        $nonExistingCurrencyId = Uuid::uuid4()->toString();
        $walletCurrency = new WalletCurrency(
            WalletCurrencyId::fromString($nonExistingCurrencyId),
            self::WALLET_CURRENCY->value
        );
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString(Uuid::uuid4()->toString())
                ),
            ],
            $walletCurrency
        );

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::UNSUPPORTED_CURRENCY_CODE,
                'transactionType' => TransactionType::DEPOSIT,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet(
                $wallet->getId(),
                $walletCurrency
            )
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseCurrencyDoesNotExist(): void
    {
        // arrange
        $nonExistingCurrencyId = Uuid::uuid4()->toString();
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($nonExistingCurrencyId),
                self::WALLET_CURRENCY->value
            )
        );

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::WALLET_CURRENCY,
                'transactionType' => TransactionType::DEPOSIT,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet($wallet->getId(), $wallet->getWalletCurrency())
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseTransactionTypeIsInvalid(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY->value
            )
        );

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::WALLET_CURRENCY,
                'transactionType' => self::INVALID_TRANSACTION_TYPE,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet(
                $wallet->getId(),
                $wallet->getWalletCurrency()
            )
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseReceiverWalletIdNotGivenAndTransferChargeTransactionType(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY->value
            )
        );

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::WALLET_CURRENCY,
                'transactionType' => TransactionType::TRANSFER_CHARGE,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet(
                $wallet->getId(),
                $wallet->getWalletCurrency()
            )
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseReceiverWalletDoesNotExistAndTransferChargeTransactionType(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY->value
            )
        );
        $nonExistingWalletId = Uuid::uuid4()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::WALLET_CURRENCY,
                'transactionType' => TransactionType::TRANSFER_CHARGE,
                'receiverWalletId' => $nonExistingWalletId,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet(
                $wallet->getId(),
                $wallet->getWalletCurrency()
            )
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseWalletDoesNotExist(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $nonExistingWalletId = Uuid::uuid4()->toString();

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $nonExistingWalletId),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::WALLET_CURRENCY,
                'transactionType' => TransactionType::DEPOSIT,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet(
                WalletId::fromString($nonExistingWalletId),
                new WalletCurrency(
                    WalletCurrencyId::fromString($currency->getId()->toString()),
                    self::WALLET_CURRENCY->value
                )
            )
        );
    }

    /**
     * @test
     *
     * @param array{transactionAmount: string, transactionCurrencyCode: SupportedCurrencies, transactionType: string} $dataset
     *
     * @dataProvider withdrawTransactionTypesWithBigAmountDataProvider
     */
    public function shouldNotStoreTransactionBecauseWithdrawTransactionTypeAndWalletBalanceIsNotEnoughToProceedTransaction(
        array $dataset
    ): void {
        // arrange
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY->value
            )
        );
        // if it's transaction for transfer charge - create receiver wallet
        if ($dataset['transactionType'] === TransactionType::TRANSFER_CHARGE->value) {
            $receiverWallet = $this->walletService->storeWallet(
                [
                    WalletOwner::create(
                        WalletOwnerExternalId::fromString($this->userId->toString())
                    ),
                ],
                new WalletCurrency(
                    WalletCurrencyId::fromString($currency->getId()->toString()),
                    self::WALLET_CURRENCY->value
                )
            );

            $dataset['receiverWalletId'] = $receiverWallet->getId()->toString();
        }

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode($dataset, JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet($wallet->getId(), $wallet->getWalletCurrency())
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseTransactionCurrencyIsDifferentThanWalletHas(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $this->walletService->storeCurrency(self::ANOTHER_CURRENCY);
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY->value
            )
        );

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::ANOTHER_CURRENCY,
                'transactionType' => TransactionType::DEPOSIT,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet($wallet->getId(), $wallet->getWalletCurrency())
        );
    }

    /** @test */
    public function shouldNotStoreTransactionBecauseTransactionCreatorDoesNotOwnWallet(): void
    {
        // arrange
        $nonExistingUserId = Uuid::uuid4()->toString();
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($nonExistingUserId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY->value
            )
        );

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode([
                'transactionAmount' => self::TRANSACTION_AMOUNT,
                'transactionCurrencyCode' => self::WALLET_CURRENCY,
                'transactionType' => TransactionType::DEPOSIT,
            ], JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        self::assertEmpty(
            $this->walletService->fetchTransactionsByWallet($wallet->getId(), $wallet->getWalletCurrency())
        );
    }

    /**
     * @test
     *
     * @param array{transactionAmount: string, transactionCurrencyCode: SupportedCurrencies, transactionType: string} $dataset
     *
     * @dataProvider transactionTypesDataProvider
     */
    public function shouldStoreDepositTransaction(array $dataset): void
    {
        // arrange
        // prepare currency and wallet for tests
        $receiverWallet = null;
        $currency = $this->walletService->storeCurrency(self::WALLET_CURRENCY);
        $wallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY->value
            )
        );

        // if it's transaction for transfer charge - create receiver wallet
        if ($dataset['transactionType'] === TransactionType::TRANSFER_CHARGE->value) {
            $receiverWallet = $this->walletService->storeWallet(
                [
                    WalletOwner::create(
                        WalletOwnerExternalId::fromString($this->userId->toString())
                    ),
                ],
                new WalletCurrency(
                    WalletCurrencyId::fromString($currency->getId()->toString()),
                    self::WALLET_CURRENCY->value
                )
            );

            $dataset['receiverWalletId'] = $receiverWallet->getId()->toString();
        }

        // act
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::API_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader(),
            content: json_encode($dataset, JSON_THROW_ON_ERROR)
        );

        // assert
        $response = $this->client->getResponse();

        // fetch all transactions for test wallet
        $transactions = $this->walletService->fetchTransactionsByWallet($wallet->getId(), $wallet->getWalletCurrency());
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode());
        self::assertNotEmpty($transactions);

        $firstTransaction = $transactions[0];
        self::assertSame($dataset['transactionType'], $firstTransaction->getType()->value);
        self::assertSame(
            $dataset['transactionAmount'],
            (string) MoneyAmountNormalizer::normalize((int) $firstTransaction->getAmount()->toString())
        );
        self::assertSame($wallet->getId()->toString(), $firstTransaction->getWalletId()->toString());
        self::assertSame(self::WALLET_CURRENCY->value, $firstTransaction->getAmount()->value->getCurrency()->getCode());

        // if it's transaction for transfer charge - check if transfer income transaction was also saved and check it data
        if ($dataset['transactionType'] === TransactionType::TRANSFER_CHARGE->value && $receiverWallet) {
            $receiverTransactions = $this->walletService->fetchTransactionsByWallet(
                $receiverWallet->getId(),
                $receiverWallet->getWalletCurrency()
            );
            $firstTransferIncomeTransaction = $receiverTransactions[0];

            self::assertSame(TransactionType::TRANSFER_INCOME->value, $firstTransferIncomeTransaction->getType()->value);
            self::assertSame(
                $dataset['transactionAmount'],
                (string) MoneyAmountNormalizer::normalize((int) $firstTransferIncomeTransaction->getAmount()->toString())
            );
            self::assertSame(
                $receiverWallet->getId()->toString(),
                $firstTransferIncomeTransaction->getWalletId()->toString()
            );
            self::assertSame(
                self::WALLET_CURRENCY->value,
                $firstTransferIncomeTransaction->getAmount()->value->getCurrency()->getCode()
            );
        }
    }

    /**
     * @return array<string, array<array{transactionAmount: string, transactionCurrencyCode: SupportedCurrencies, transactionType: string}>>
     */
    public function transactionTypesDataProvider(): array
    {
        return [
            TransactionType::DEPOSIT->value => [
                [
                    'transactionAmount' => self::TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::DEPOSIT->value,
                ],
            ],
            TransactionType::DEPOSIT->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::DEPOSIT->value,
                ],
            ],
            TransactionType::WITHDRAW->value => [
                [
                    'transactionAmount' => self::TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::WITHDRAW->value,
                ],
            ],
            TransactionType::WITHDRAW->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::WITHDRAW->value,
                ],
            ],
            TransactionType::TRANSFER_CHARGE->value => [
                [
                    'transactionAmount' => self::TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::TRANSFER_CHARGE->value,
                ],
            ],
            TransactionType::TRANSFER_CHARGE->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::TRANSFER_CHARGE->value,
                ],
            ],
            TransactionType::TRANSFER_INCOME->value => [
                [
                    'transactionAmount' => self::TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::TRANSFER_INCOME->value,
                ],
            ],
            TransactionType::TRANSFER_INCOME->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::TRANSFER_INCOME->value,
                ],
            ],
            TransactionType::EXPENSE->value => [
                [
                    'transactionAmount' => self::TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::EXPENSE->value,
                ],
            ],
            TransactionType::EXPENSE->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::EXPENSE->value,
                ],
            ],
        ];
    }

    /**
     * @return array<string, array<array{transactionAmount: string, transactionCurrencyCode: SupportedCurrencies, transactionType: string}>>
     */
    public function withdrawTransactionTypesWithBigAmountDataProvider(): array
    {
        return [
            TransactionType::WITHDRAW->value => [
                [
                    'transactionAmount' => self::BIG_TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::WITHDRAW->value,
                ],
            ],
            TransactionType::WITHDRAW->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::BIG_TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::WITHDRAW->value,
                ],
            ],
            TransactionType::TRANSFER_CHARGE->value => [
                [
                    'transactionAmount' => self::BIG_TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::TRANSFER_CHARGE->value,
                ],
            ],
            TransactionType::TRANSFER_CHARGE->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::BIG_TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::TRANSFER_CHARGE->value,
                ],
            ],
            TransactionType::EXPENSE->value => [
                [
                    'transactionAmount' => self::BIG_TRANSACTION_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::EXPENSE->value,
                ],
            ],
            TransactionType::EXPENSE->value . '_DECIMAL' => [
                [
                    'transactionAmount' => self::BIG_TRANSACTION_DECIMAL_AMOUNT,
                    'transactionCurrencyCode' => self::WALLET_CURRENCY,
                    'transactionType' => TransactionType::EXPENSE->value,
                ],
            ],
        ];
    }
}
