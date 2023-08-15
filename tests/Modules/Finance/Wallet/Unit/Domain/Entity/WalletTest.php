<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\Domain\Entity;

use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Wallet\Application\Service\TransactionAmountCreator as TransactionAmountCreatorInterface;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCreatorDoesNotOwnWallet;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCurrencyIsDifferentWalletHas;
use App\Modules\Finance\Wallet\Domain\Exception\WalletBalanceIsNotEnoughToProceedTransaction;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Infrastructure\Adapter\TransactionAmountCreator;
use App\Tests\Modules\Finance\Wallet\TestUtils\Mother\WalletMother;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class WalletTest extends TestCase
{
    private const CURRENCY_CODE = SupportedCurrencies::PLN;
    private const ANOTHER_CURRENCY_CODE = SupportedCurrencies::USD;
    private const TRANSACTION_AMOUNT = '100';
    private const TRANSACTION_BIG_AMOUNT = '500';
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
    public function shouldRegisterTransaction(TransactionType $type): void
    {
        // arrange
        $userId = Uuid::uuid4()->toString();
        $currencyId = Uuid::uuid4()->toString();

        $sut = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::CURRENCY_CODE->value
            )
        );

        $transaction = Transaction::create(
            $sut->getId(),
            $this->transactionAmountCreator->create(self::TRANSACTION_AMOUNT, self::CURRENCY_CODE->value),
            $type,
            TransactionCreator::fromString($userId)
        );

        // act
        $sut->registerTransaction($transaction);

        // assert
        $transactions = $sut->getTransactions();
        $firstTransaction = $transactions[0];

        self::assertSame(self::TRANSACTION_AMOUNT, $firstTransaction->getAmount()->toString());
        self::assertSame(self::CURRENCY_CODE->value, $firstTransaction->getAmount()->value->getCurrency()->getCode());
        self::assertSame($type->value, $firstTransaction->getType()->value);
    }

    /**
     * @test
     *
     * @dataProvider bigAmountTransactionsDataProvider
     */
    public function shouldNotRegisterTransactionBecauseWalletBalanceIsNotEnoughToProceedTransaction(
        TransactionType $type
    ): void {
        // arrange
        $userId = Uuid::uuid4()->toString();
        $currencyId = Uuid::uuid4()->toString();

        $sut = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::CURRENCY_CODE->value
            )
        );

        $transaction = Transaction::create(
            $sut->getId(),
            $this->transactionAmountCreator->create(self::TRANSACTION_BIG_AMOUNT, self::CURRENCY_CODE->value),
            $type,
            TransactionCreator::fromString($userId)
        );

        // assert
        $this->expectException(WalletBalanceIsNotEnoughToProceedTransaction::class);

        // act
        $sut->registerTransaction($transaction);
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseTransactionCreatorDoesNotOwnWallet(): void
    {
        // arrange
        $userId = Uuid::uuid4()->toString();
        $nonExistingUser = Uuid::uuid4()->toString();
        $currencyId = Uuid::uuid4()->toString();

        $sut = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($nonExistingUser)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::CURRENCY_CODE->value
            )
        );

        $transaction = Transaction::create(
            $sut->getId(),
            $this->transactionAmountCreator->create(self::TRANSACTION_AMOUNT, self::CURRENCY_CODE->value),
            TransactionType::DEPOSIT,
            TransactionCreator::fromString($userId)
        );

        // assert
        $this->expectException(TransactionCreatorDoesNotOwnWallet::class);

        // act
        $sut->registerTransaction($transaction);
    }

    /** @test */
    public function shouldRegisterTransactionDespiteCreatorDoesNotExistBecauseItIsTransferIncomeTransactionType(): void
    {
        // arrange
        $userId = Uuid::uuid4()->toString();
        $nonExistingUser = Uuid::uuid4()->toString();
        $currencyId = Uuid::uuid4()->toString();

        $sut = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($nonExistingUser)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::CURRENCY_CODE->value
            )
        );

        $transaction = Transaction::create(
            $sut->getId(),
            $this->transactionAmountCreator->create(self::TRANSACTION_AMOUNT, self::CURRENCY_CODE->value),
            TransactionType::TRANSFER_INCOME,
            TransactionCreator::fromString($userId)
        );

        // act
        $sut->registerTransaction($transaction);

        // assert
        $transactions = $sut->getTransactions();
        $firstTransaction = $transactions[0];

        self::assertSame(self::TRANSACTION_AMOUNT, $firstTransaction->getAmount()->toString());
        self::assertSame(self::CURRENCY_CODE->value, $firstTransaction->getAmount()->value->getCurrency()->getCode());
        self::assertSame(TransactionType::TRANSFER_INCOME->value, $firstTransaction->getType()->value);
    }

    /** @test */
    public function shouldNotRegisterTransactionBecauseCurrencyIsDifferentThanWalletHas(): void
    {
        // arrange
        $userId = Uuid::uuid4()->toString();
        $currencyId = Uuid::uuid4()->toString();

        $sut = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::CURRENCY_CODE->value
            )
        );

        $transaction = Transaction::create(
            $sut->getId(),
            $this->transactionAmountCreator->create(self::TRANSACTION_AMOUNT, self::ANOTHER_CURRENCY_CODE->value),
            TransactionType::DEPOSIT,
            TransactionCreator::fromString($userId)
        );

        // assert
        $this->expectException(TransactionCurrencyIsDifferentWalletHas::class);

        // act
        $sut->registerTransaction($transaction);
    }

    /**
     * @test
     *
     * @dataProvider manyTransactionsWithExpectedBalanceDataProvider
     */
    public function walletBalanceShouldBeAsExpectedAfterSomeTransactions(
        array $transactions,
        string $expectedBalance
    ): void {
        // arrange
        $userId = Uuid::uuid4()->toString();
        $currencyId = Uuid::uuid4()->toString();
        $preparedTransactions = [];

        $sut = WalletMother::create(
            [
                WalletOwner::create(
                    WalletOwnerExternalId::fromString($userId)
                ),
            ],
            new WalletCurrency(
                WalletCurrencyId::fromString($currencyId),
                self::CURRENCY_CODE->value
            )
        );

        /** @var array{type: TransactionType, amount: string} $transaction */
        foreach ($transactions as $transaction) {
            $preparedTransactions[] = Transaction::create(
                $sut->getId(),
                $this->transactionAmountCreator->create($transaction['amount'], self::CURRENCY_CODE->value),
                $transaction['type'],
                TransactionCreator::fromString($userId)
            );
        }

        // act
        foreach ($preparedTransactions as $preparedTransaction) {
            $sut->registerTransaction($preparedTransaction);
        }

        // assert
        self::assertSame($expectedBalance, $sut->getBalance()->toString());
    }

    public function manyTransactionsWithExpectedBalanceDataProvider(): array
    {
        return [
            [
                [
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '500',
                    ],
                    [
                        'type' => TransactionType::WITHDRAW,
                        'amount' => '200',
                    ],
                    [
                        'type' => TransactionType::TRANSFER_INCOME,
                        'amount' => '250',
                    ],
                ],
                '650',
            ],
            [
                [
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '500',
                    ],
                    [
                        'type' => TransactionType::DEPOSIT,
                        'amount' => '500',
                    ],
                    [
                        'type' => TransactionType::WITHDRAW,
                        'amount' => '100',
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
                '0',
            ],
        ];
    }

    public function transactionsDataProvider(): array
    {
        return [
            TransactionType::WITHDRAW->value => [
                TransactionType::WITHDRAW,
            ],
            TransactionType::DEPOSIT->value => [
                TransactionType::DEPOSIT,
            ],
            TransactionType::EXPENSE->value => [
                TransactionType::EXPENSE,
            ],
            TransactionType::TRANSFER_INCOME->value => [
                TransactionType::TRANSFER_INCOME,
            ],
            TransactionType::TRANSFER_CHARGE->value => [
                TransactionType::TRANSFER_CHARGE,
            ],
        ];
    }

    public function bigAmountTransactionsDataProvider(): array
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
