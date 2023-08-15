<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Entity;

use App\Modules\Finance\Wallet\Domain\Exception\InvalidTransactionType;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCreatorDoesNotOwnWallet;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCurrencyIsDifferentWalletHas;
use App\Modules\Finance\Wallet\Domain\Exception\WalletBalanceIsNotEnoughToProceedTransaction;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Shared\Domain\ValueObject\WalletId;

final class Wallet
{
    private array $transactions = [];

    /**
     * @param array<WalletOwner> $owners
     */
    private function __construct(
        private readonly WalletId $id,
        private readonly string $name,
        private readonly array $owners,
        private WalletBalance $balance,
        private readonly WalletCurrency $walletCurrency
    ) {
    }

    public static function create(
        string $name,
        array $owners,
        WalletBalance $startBalance,
        WalletCurrency $walletCurrency
    ): self {
        return new self(
            WalletId::generate(),
            $name,
            $owners,
            $startBalance,
            $walletCurrency
        );
    }

    public static function reproduce(
        WalletId $id,
        string $name,
        array $owners,
        WalletBalance $currentBalance,
        WalletCurrency $walletCurrency
    ): self {
        return new self(
            $id,
            $name,
            $owners,
            $currentBalance,
            $walletCurrency
        );
    }

    /**
     * @throws TransactionCreatorDoesNotOwnWallet
     * @throws TransactionCurrencyIsDifferentWalletHas
     * @throws WalletBalanceIsNotEnoughToProceedTransaction
     * @throws InvalidTransactionType
     */
    public function registerTransaction(Transaction $transaction): void
    {
        if (
            !$this->doesTransactionCreatorOwnTheWallet($transaction->getTransactionCreator()) &&
            $transaction->getType()->value !== TransactionType::TRANSFER_INCOME->value
        ) {
            throw TransactionCreatorDoesNotOwnWallet::withId($transaction->getTransactionCreator()->toString());
        }

        $transactionCurrencyCode = $transaction->getAmount()->value->getCurrency()->getCode();
        if (!$this->isTransactionCurrencyTheSameAsTheWalletsOne($transactionCurrencyCode)) {
            throw TransactionCurrencyIsDifferentWalletHas::withCurrenciesCodes(
                $transactionCurrencyCode,
                $this->balance->value->getCurrency()->getCode()
            );
        }

        if (
            $transaction->isWithdrawType() &&
            !$this->balance->value->greaterThanOrEqual($transaction->getAmount()->value)
        ) {
            throw WalletBalanceIsNotEnoughToProceedTransaction::withNumbers(
                $this->balance->value,
                $transaction->getAmount()->value
            );
        }

        $this->balance = match ($transaction->getType()) {
            TransactionType::DEPOSIT,
            TransactionType::TRANSFER_INCOME => new WalletBalance($this->balance->value->add($transaction->getAmount()->value)),
            TransactionType::EXPENSE,
            TransactionType::TRANSFER_CHARGE,
            TransactionType::WITHDRAW => new WalletBalance($this->balance->value->subtract($transaction->getAmount()->value)),
            default => throw InvalidTransactionType::withType($transaction->getType()->value),
        };

        $this->addTransaction($transaction);
    }

    public function getId(): WalletId
    {
        return $this->id;
    }

    public function getWalletCurrency(): WalletCurrency
    {
        return $this->walletCurrency;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOwners(): array
    {
        return $this->owners;
    }

    public function getBalance(): WalletBalance
    {
        return $this->balance;
    }

    public function getCurrencyId(): WalletCurrencyId
    {
        return $this->walletCurrency->currencyId;
    }

    public function getCurrencyCode(): string
    {
        return $this->walletCurrency->currencyCode;
    }

    /** @return Transaction[] */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    private function doesTransactionCreatorOwnTheWallet(TransactionCreator $transactionCreator): bool
    {
        foreach ($this->getOwners() as $owner) {
            if ($owner->externalId->toString() === $transactionCreator->toString()) {
                return true;
            }
        }

        return false;
    }

    private function isTransactionCurrencyTheSameAsTheWalletsOne(string $transactionCurrency): bool
    {
        return $this->getBalance()->value->getCurrency()->getCode() === $transactionCurrency;
    }

    private function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }
}
