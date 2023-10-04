<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\Http;

use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Wallet\Application\Service\TransactionAmountCreator as TransactionAmountCreatorInterface;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Infrastructure\Adapter\TransactionAmountCreator;
use App\Tests\Modules\Finance\Wallet\Integration\TestUtils\WalletService;
use App\Tests\SharedInfrastructure\IntegrationTestBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class FetchAllWalletsTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = 'api/finance/wallet';
    private const WALLET_CURRENCY_CODE = SupportedCurrencies::PLN->value;
    private const WALLETS_PER_PAGE = 1;
    private WalletService $walletService;
    private TransactionAmountCreatorInterface $transactionAmountCreator;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        /** @var WalletService $walletService */
        $walletService = $this->container->get(WalletService::class);
        /** @var TransactionAmountCreator $transactionAmountCreator */
        $transactionAmountCreator = $this->container->get(TransactionAmountCreator::class);

        $this->walletService = $walletService;
        $this->transactionAmountCreator = $transactionAmountCreator;
    }

    /** @test */
    public function shouldNotFetchWalletsWithUnauthorizedUser(): void
    {
        // act
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_URL
        );

        // assert
        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider walletsTransactionsProvider
     *
     * @test
     *
     * @param array{type: TransactionType, amount: string} $firstWalletTransactions
     * @param array{type: TransactionType, amount: string} $secondWalletTransactions
     */
    public function shouldFetchWalletWithCorrectBalance(
        array $firstWalletTransactions,
        array $secondWalletTransactions,
        string $firstWalletExpectedBalance,
        string $secondWalletExpectedBalance
    ): void {
        // arrange
        $currency = $this->walletService->storeCurrency(SupportedCurrencies::from(self::WALLET_CURRENCY_CODE));

        $firstWallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY_CODE
            )
        );

        $secondWallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY_CODE
            )
        );

        /** @var array{type: TransactionType, amount: string} $transaction */
        foreach ($firstWalletTransactions as $transaction) {
            $transaction = Transaction::create(
                $firstWallet->getId(),
                $this->transactionAmountCreator->create($transaction['amount'], self::WALLET_CURRENCY_CODE),
                $transaction['type'],
                TransactionCreator::fromString($this->userId->toString())
            );

            $this->walletService->storeTransaction($transaction);
        }

        /** @var array{type: TransactionType, amount: string} $transaction */
        foreach ($secondWalletTransactions as $transaction) {
            $transaction = Transaction::create(
                $secondWallet->getId(),
                $this->transactionAmountCreator->create($transaction['amount'], self::WALLET_CURRENCY_CODE),
                $transaction['type'],
                TransactionCreator::fromString($this->userId->toString())
            );

            $this->walletService->storeTransaction($transaction);
        }

        // act
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_URL,
            server: $this->getAuthHeader()
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $result */
        $result = $response->getContent();

        /** @var object{items: array<object{id: string, balance: string, currencyCode: string}>} $parsedResponse */
        $parsedResponse = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        [$firstFetchedWallet, $secondFetchedWallet] = $parsedResponse->items;

        $fetchedWallets = [
            $firstFetchedWallet->id => $firstFetchedWallet,
            $secondFetchedWallet->id => $secondFetchedWallet,
        ];

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertArrayHasKey($firstWallet->getId()->toString(), $fetchedWallets);
        self::assertSame(
            $firstWalletExpectedBalance,
            $fetchedWallets[$firstWallet->getId()->toString()]->balance
        );
        self::assertSame(
            self::WALLET_CURRENCY_CODE,
            $fetchedWallets[$firstWallet->getId()->toString()]->currencyCode
        );

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertArrayHasKey($secondWallet->getId()->toString(), $fetchedWallets);
        self::assertSame(
            $secondWalletExpectedBalance,
            $fetchedWallets[$secondWallet->getId()->toString()]->balance
        );
        self::assertSame(
            self::WALLET_CURRENCY_CODE,
            $fetchedWallets[$secondWallet->getId()->toString()]->currencyCode
        );
    }

    /** @test */
    public function shouldFetchSpecificAmountOfWalletsAccordingPagination(): void
    {
        // arrange
        $currency = $this->walletService->storeCurrency(SupportedCurrencies::from(self::WALLET_CURRENCY_CODE));

        $firstWallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY_CODE
            )
        );

        $secondWallet = $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($this->userId->toString())
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY_CODE
            )
        );

        // act
        $this->client->request(
            Request::METHOD_GET,
            self::ENDPOINT_URL . '?perPage=' . self::WALLETS_PER_PAGE,
            server: $this->getAuthHeader()
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $result */
        $result = $response->getContent();

        /** @var object{items: array<object{id: string, balance: string, currencyCode: string}>} $parsedResponse */
        $parsedResponse = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        self::assertCount(self::WALLETS_PER_PAGE, $parsedResponse->items);
    }

    /**
     * @return array<array<string|array<array{type: TransactionType, amount: string}>>>
     */
    public function walletsTransactionsProvider(): array
    {
        return [
            [
                [
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '501.25',
                    ],
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '500',
                    ],
                    [
                        'type' => TransactionType::WITHDRAW,
                        'amount' => '101.25',
                    ],
                    [
                        'type' => TransactionType::TRANSFER_CHARGE,
                        'amount' => '500',
                    ],
                    [
                        'type' => TransactionType::EXPENSE,
                        'amount' => '500',
                    ],
                ],
                [
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '504.19',
                    ],
                    [
                        'type' => TransactionType::WITHDRAW,
                        'amount' => '204.19',
                    ],
                    [
                        'type' => TransactionType::TRANSFER_INCOME,
                        'amount' => '250',
                    ],
                ],
                '0.00',
                '650.00',
            ],
        ];
    }
}
