<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\Http;

use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Wallet\Application\Service\TransactionAmountCreator as TransactionAmountCreatorInterface;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
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
final class FetchWalletTransactionsTest extends IntegrationTestBase
{
    private const ENDPOINT_URL = '/api/finance/wallet/%s/transaction';
    private const WALLET_CURRENCY_CODE = SupportedCurrencies::PLN->value;
    private const FIRST_TRANSACTION_AMOUNT = '50.25';
    private const FIRST_TRANSACTION_TYPE = TransactionType::WITHDRAW;
    private const SECOND_TRANSACTION_TYPE = TransactionType::DEPOSIT;
    private const SECOND_TRANSACTION_AMOUNT = '100.00';
    private const PAGINATION_LIMIT = 1;
    private WalletService $walletService;
    private TransactionAmountCreatorInterface $transactionAmountCreator;

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpAuthUserProvider();

        /** @var WalletService $walletService */
        $walletService = $this->container->get(WalletService::class);
        $this->walletService = $walletService;

        /** @var TransactionAmountCreator $transactionAmountCreator */
        $transactionAmountCreator = $this->container->get(TransactionAmountCreator::class);
        $this->transactionAmountCreator = $transactionAmountCreator;
    }

    /** @test */
    public function shouldNotReturnWalletTransactionsWithUnauthorizedUser(): void
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

    /** @test */
    public function shouldReturnWalletTransactions(): void
    {
        // arrange
        $wallet = $this->prepareWallet($this->userId->toString());
        $transactions = $this->enrichWalletWithTransactions($wallet);

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

        /** @var object{items: array<object{id: string, walletId: string, amount: string, type: string, creatorId: string, externalId: string, createdAt: string}>} $fetchedTransactionsCollection */
        $fetchedTransactionsCollection = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        $fetchedTransactions = $fetchedTransactionsCollection->items;

        self::assertCount(count($transactions), $fetchedTransactions);
        [$secondTransaction, $firstTransaction] = $fetchedTransactions;

        self::assertSame($wallet->getId()->toString(), $firstTransaction->walletId);
        self::assertSame($wallet->getId()->toString(), $secondTransaction->walletId);

        self::assertSame(self::FIRST_TRANSACTION_AMOUNT, $firstTransaction->amount);
        self::assertSame(self::SECOND_TRANSACTION_AMOUNT, $secondTransaction->amount);

        self::assertSame(self::FIRST_TRANSACTION_TYPE->value, $firstTransaction->type);
        self::assertSame(self::SECOND_TRANSACTION_TYPE->value, $secondTransaction->type);
    }

    /** @test */
    public function shouldReturnWalletTransactionsAccordingPagination(): void
    {
        // arrange
        $wallet = $this->prepareWallet($this->userId->toString());
        $this->enrichWalletWithTransactions($wallet);

        // act
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_URL, $wallet->getId()->toString()) . '?perPage=' . self::PAGINATION_LIMIT,
            server: $this->getAuthHeader()
        );

        // assert
        $response = $this->client->getResponse();
        /** @var string $result */
        $result = $response->getContent();

        /** @var object{items: array<object{id: string, walletId: string, amount: string, type: string, creatorId: string, externalId: string}>} $fetchedTransactionsCollection */
        $fetchedTransactionsCollection = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        $fetchedTransactions = $fetchedTransactionsCollection->items;

        self::assertCount(self::PAGINATION_LIMIT, $fetchedTransactions);
    }

    /** @test */
    public function shouldNotReturnWalletTransactionsBecauseRequesterDoesNotOwnTheWallet(): void
    {
        // arrange
        $wallet = $this->prepareWallet(Uuid::uuid4()->toString());

        // act
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_URL, $wallet->getId()->toString()),
            server: $this->getAuthHeader()
        );

        // assert
        self::assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /** @test */
    public function shouldNotReturnWalletTransactionsBecauseWalletDoesNotExist(): void
    {
        // arrange
        $walletId = Uuid::uuid4()->toString();

        // act
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::ENDPOINT_URL, $walletId),
            server: $this->getAuthHeader()
        );

        // assert
        self::assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    private function prepareWallet(string $ownerId): Wallet
    {
        $currency = $this->walletService->storeCurrency(SupportedCurrencies::from(self::WALLET_CURRENCY_CODE));

        return $this->walletService->storeWallet(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($ownerId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currency->getId()->toString()),
                self::WALLET_CURRENCY_CODE
            )
        );
    }

    /**
     * @return array<array{type: TransactionType, amount: string}>
     */
    private function enrichWalletWithTransactions(Wallet $wallet): array
    {
        $transactions = [
            [
                'type' => self::FIRST_TRANSACTION_TYPE,
                'amount' => self::FIRST_TRANSACTION_AMOUNT,
            ],
            [
                'type' => self::SECOND_TRANSACTION_TYPE,
                'amount' => self::SECOND_TRANSACTION_AMOUNT,
            ],
        ];

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

        return $transactions;
    }
}
