<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\Application\Service;

use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\Exception\CannotRegisterTransferTransactionWithoutReceiverWalletIdException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyCodeIsNotSupportedException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Wallet\Application\Exception\WalletBalanceIsNotEnoughToProceedTransactionException;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Service\CurrencyFetcher;
use App\Modules\Finance\Wallet\Application\Service\TransactionAmountCreator as TransactionAmountCreatorInterface;
use App\Modules\Finance\Wallet\Application\Service\TransactionRegistrar;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionReceiverWalletId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Infrastructure\Adapter\TransactionAmountCreator;
use App\Tests\Modules\Finance\Wallet\TestUtils\Mother\WalletMother;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler\FetchCurrencyByCodeHandlerStub;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler\FetchUserIdByTokenHandlerStub;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Repository\TransactionRepositoryFake;
use App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\Repository\WalletRepositoryFake;
use App\Tests\SharedInfrastructure\Unit\Application\Messenger\QueryBusFake;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class TransactionRegistrarTest extends TestCase
{
    private const TRANSACTION_AMOUNT = '100';
    private const BIG_TRANSACTION_AMOUNT = '500';
    private const TRANSACTION_CURRENCY = 'PLN';
    private const ANOTHER_TRANSACTION_CURRENCY = 'USD';
    private const UNKNOWN_CURRENCY = 'xxx';
    private TransactionAmountCreatorInterface $transactionAmountCreator;

    public function setUp(): void
    {
        $this->transactionAmountCreator = new TransactionAmountCreator();
    }

    /**
     * @test
     *
     * @dataProvider transactionsDataProvider
     */
    public function shouldRegisterTransaction(
        TransactionType $transactionType,
        TransactionAmount $amount,
        TransactionCreator $transactionCreator,
        ?TransactionExternalId $externalId = null,
    ): void {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $receiverUserId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                $amount->value->getCurrency()->getCode()
            )
        );
        $receiverWalletId = null;

        $walletRepository = new WalletRepositoryFake();
        $walletRepository->store($wallet);

        if (TransactionType::TRANSFER_CHARGE->value === $transactionType->value) {
            $transactionReceiverWallet = WalletMother::create(
                [
                    WalletOwner::create(
                        WalletOwnerExternalId::fromString($receiverUserId)
                    ),
                ],
                new WalletCurrency(
                    WalletCurrencyId::fromString($currencyId),
                    $amount->value->getCurrency()->getCode()
                )
            );

            $walletRepository->store($transactionReceiverWallet);
            $receiverWalletId = TransactionReceiverWalletId::fromString($transactionReceiverWallet->getId()->toString());
        }

        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // act
        $sut->register(
            $transactionType,
            $wallet->getId(),
            $amount,
            $transactionCreator,
            $externalId,
            $receiverWalletId
        );

        // assert
        $transactions = $transactionRepository->fetchTransactionsByWallet(
            $wallet->getId(),
            $wallet->getWalletCurrency()
        );
        self::assertNotEmpty($transactions);

        $firstTransaction = $transactions[0];
        self::assertSame($transactionType->value, $firstTransaction->getType()->value);
        self::assertSame($amount->toString(), $firstTransaction->getAmount()->toString());
        self::assertSame($userId, $firstTransaction->getTransactionCreator()->toString());

        if (TransactionType::TRANSFER_CHARGE->value === $transactionType->value) {
            $transferReceiverIncomeTransaction = $transactionReceiverWallet->getTransactions()[0];

            self::assertSame(
                TransactionType::TRANSFER_INCOME->value,
                $transferReceiverIncomeTransaction->getType()->value
            );
            self::assertSame($amount->toString(), $transferReceiverIncomeTransaction->getAmount()->toString());
            self::assertSame($userId, $transferReceiverIncomeTransaction->getTransactionCreator()->toString());
        }
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseWalletDoesNotExist(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(WalletDoesNotExistException::class);

        // act
        $sut->register(
            TransactionType::DEPOSIT,
            $wallet->getId(),
            new TransactionAmount(new Money(self::TRANSACTION_AMOUNT, new Currency(self::TRANSACTION_CURRENCY))),
            new TransactionCreator(Uuid::uuid4()->toString())
        );
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseCurrencyCodeIsNotSupported(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(CurrencyCodeIsNotSupportedException::class);

        // act
        $sut->register(
            TransactionType::DEPOSIT,
            $wallet->getId(),
            new TransactionAmount(
                new Money(
                    self::TRANSACTION_AMOUNT,
                    new Currency(self::UNKNOWN_CURRENCY)
                )
            ),
            new TransactionCreator(Uuid::uuid4()->toString())
        );
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseCurrencyDoesNotExist(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub());
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $walletRepository->store($wallet);
        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(CurrencyDoesNotExistException::class);

        // act
        $sut->register(
            TransactionType::DEPOSIT,
            $wallet->getId(),
            new TransactionAmount(
                new Money(self::TRANSACTION_AMOUNT, new Currency(self::TRANSACTION_CURRENCY))
            ),
            new TransactionCreator(Uuid::uuid4()->toString())
        );
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWallet(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $nonExistingUserId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($nonExistingUserId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $walletRepository->store($wallet);
        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(TransactionCreatorDoesNotOwnWalletException::class);

        // act
        $sut->register(
            TransactionType::DEPOSIT,
            $wallet->getId(),
            new TransactionAmount(
                new Money(
                    self::TRANSACTION_AMOUNT,
                    new Currency(self::TRANSACTION_CURRENCY)
                )
            ),
            new TransactionCreator(Uuid::uuid4()->toString())
        );
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseTransactionCurrencyIsDifferentWalletHas(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::ANOTHER_TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $walletRepository->store($wallet);
        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(TransactionCurrencyIsDifferentWalletHasException::class);

        // act
        $sut->register(
            TransactionType::DEPOSIT,
            $wallet->getId(),
            new TransactionAmount(
                new Money(
                    self::TRANSACTION_AMOUNT,
                    new Currency(self::TRANSACTION_CURRENCY)
                )
            ),
            new TransactionCreator(Uuid::uuid4()->toString())
        );
    }

    /**
     * @test
     *
     * @dataProvider withdrawalTransactionsTypeDataProvider
     */
    public function shouldNotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedTransaction(
        TransactionType $type
    ): void {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();
        $receiverUserId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $walletRepository->store($wallet);

        $transactionReceiverWalletId = null;
        if ($type->value === TransactionType::TRANSFER_CHARGE->value) {
            $transactionReceiverWallet = WalletMother::create(
                [
                    WalletOwner::create(
                        WalletOwnerExternalId::fromString($receiverUserId)
                    ),
                ],
                new WalletCurrency(
                    WalletCurrencyId::fromString($currencyId),
                    self::TRANSACTION_CURRENCY
                )
            );
            $receiverWalletId = $transactionReceiverWallet->getId()->toString();

            $walletRepository->store($transactionReceiverWallet);
            $transactionReceiverWalletId = TransactionReceiverWalletId::fromString($receiverWalletId);
        }

        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(WalletBalanceIsNotEnoughToProceedTransactionException::class);

        // act
        $sut->register(
            $type,
            $wallet->getId(),
            $this->transactionAmountCreator->create(
                self::BIG_TRANSACTION_AMOUNT,
                self::TRANSACTION_CURRENCY
            ),
            new TransactionCreator(Uuid::uuid4()->toString()),
            null,
            $transactionReceiverWalletId
        );
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseTransactionCreatorDoesNotExist(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub());

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::ANOTHER_TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $walletRepository->store($wallet);
        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(TransactionCreatorDoesNotExistException::class);

        // act
        $sut->register(
            TransactionType::DEPOSIT,
            $wallet->getId(),
            new TransactionAmount(
                new Money(
                    self::TRANSACTION_AMOUNT,
                    new Currency(self::TRANSACTION_CURRENCY)
                )
            ),
            new TransactionCreator(Uuid::uuid4()->toString())
        );
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseCannotRegisterTransferTransactionWithoutReceiverWalletId(): void
    {
        // arrange
        $currencyId = Uuid::uuid4()->toString();
        $userId = Uuid::uuid4()->toString();

        $queryBus = new QueryBusFake();
        $queryBus->addHandler(FetchCurrencyByCode::class, new FetchCurrencyByCodeHandlerStub($currencyId));
        $queryBus->addHandler(FetchUserIdByToken::class, new FetchUserIdByTokenHandlerStub($userId));

        $wallet = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::ANOTHER_TRANSACTION_CURRENCY
            )
        );

        $walletRepository = new WalletRepositoryFake();
        $walletRepository->store($wallet);
        $transactionRepository = new TransactionRepositoryFake();

        $sut = new TransactionRegistrar(
            new CurrencyFetcher($queryBus),
            $walletRepository,
            $transactionRepository,
            $queryBus
        );

        // assert
        self::expectException(CannotRegisterTransferTransactionWithoutReceiverWalletIdException::class);

        // act
        $sut->register(
            TransactionType::TRANSFER_CHARGE,
            $wallet->getId(),
            new TransactionAmount(
                new Money(
                    self::TRANSACTION_AMOUNT,
                    new Currency(self::TRANSACTION_CURRENCY)
                )
            ),
            new TransactionCreator(Uuid::uuid4()->toString())
        );
    }

    public function transactionsDataProvider(): array
    {
        return [
            TransactionType::WITHDRAW->value => [
                TransactionType::WITHDRAW,
                new TransactionAmount(
                    new Money(self::TRANSACTION_AMOUNT, new Currency(self::TRANSACTION_CURRENCY))
                ),
                new TransactionCreator(Uuid::uuid4()->toString()),
                null,
            ],
            TransactionType::DEPOSIT->value => [
                TransactionType::DEPOSIT,
                new TransactionAmount(
                    new Money(self::TRANSACTION_AMOUNT, new Currency(self::TRANSACTION_CURRENCY))
                ),
                new TransactionCreator(Uuid::uuid4()->toString()),
                null,
            ],
            TransactionType::EXPENSE->value => [
                TransactionType::EXPENSE,
                new TransactionAmount(
                    new Money(self::TRANSACTION_AMOUNT, new Currency(self::TRANSACTION_CURRENCY))
                ),
                new TransactionCreator(Uuid::uuid4()->toString()),
                null,
            ],
            TransactionType::TRANSFER_INCOME->value => [
                TransactionType::TRANSFER_INCOME,
                new TransactionAmount(
                    new Money(self::TRANSACTION_AMOUNT, new Currency(self::TRANSACTION_CURRENCY))
                ),
                new TransactionCreator(Uuid::uuid4()->toString()),
                null,
            ],
            TransactionType::TRANSFER_CHARGE->value => [
                TransactionType::TRANSFER_CHARGE,
                new TransactionAmount(
                    new Money(self::TRANSACTION_AMOUNT, new Currency(self::TRANSACTION_CURRENCY))
                ),
                new TransactionCreator(Uuid::uuid4()->toString()),
                null,
            ],
        ];
    }

    public function withdrawalTransactionsTypeDataProvider(): array
    {
        return [
            TransactionType::WITHDRAW->value => [
                TransactionType::WITHDRAW,
            ],
            TransactionType::EXPENSE->value => [
                TransactionType::EXPENSE,
            ],
            TransactionType::TRANSFER_CHARGE->value => [
                TransactionType::TRANSFER_CHARGE,
            ],
        ];
    }
}
