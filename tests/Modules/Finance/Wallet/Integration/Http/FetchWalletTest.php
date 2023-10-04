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
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/** @group integration */
final class FetchWalletTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = 'api/finance/wallet/%s';
    private const WALLET_CURRENCY_CODE = SupportedCurrencies::PLN->value;
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
        // arrange
        $walletId = Uuid::uuid4()->toString();

        // act
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_URL, $walletId)
        );

        // assert
        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @test
     *
     * @dataProvider walletsTransactionsProvider
     *
     * @param array{type: TransactionType, amount: string} $transactions
     */
    public function shouldFetchWalletWithCorrectBalance(
        array $transactions,
        string $expectedBalance
    ): void {
        // arrange
        $currency = $this->walletService->storeCurrency(SupportedCurrencies::from(self::WALLET_CURRENCY_CODE));

        $wallet = $this->walletService->storeWallet(
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
        foreach ($transactions as $transaction) {
            $transaction = Transaction::create(
                $wallet->getId(),
                $this->transactionAmountCreator->create($transaction['amount'], self::WALLET_CURRENCY_CODE),
                $transaction['type'],
                TransactionCreator::fromString($this->userId->toString())
            );

            $this->walletService->storeTransaction($transaction);
        }

        // act
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader()
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $result */
        $result = $response->getContent();

        /** @var object{id: string, balance: string, currencyCode: string} $fetchedWallet */
        $fetchedWallet = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame($wallet->getId()->toString(), $fetchedWallet->id);
        self::assertSame($expectedBalance, $fetchedWallet->balance);
        self::assertSame(self::WALLET_CURRENCY_CODE, $fetchedWallet->currencyCode);
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
                        'amount' => '501.28',
                    ],
                    [
                        'type' => TransactionType::WITHDRAW,
                        'amount' => '201.28',
                    ],
                    [
                        'type' => TransactionType::TRANSFER_INCOME,
                        'amount' => '250',
                    ],
                ],
                '650.00',
            ],
            [
                [
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '500.89',
                    ],
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '500',
                    ],
                    [
                        'type' => TransactionType::WITHDRAW,
                        'amount' => '100.89',
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
                '0.00',
            ],
        ];
    }
}
