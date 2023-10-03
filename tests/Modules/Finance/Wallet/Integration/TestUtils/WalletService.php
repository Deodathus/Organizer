<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\TestUtils;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Wallet\Application\ReadModel\TransactionReadModel;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletOwnerExternalId;
use App\Modules\Finance\Wallet\Infrastructure\Repository\WalletRepository;
use App\Shared\Domain\ValueObject\WalletId;
use App\Tests\Modules\Finance\Wallet\TestUtils\Mother\WalletMother;

final readonly class WalletService
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private WalletRepository $walletRepository,
        private TransactionRepository $transactionRepository,
        private TransactionReadModel $transactionReadModel
    ) {
    }

    public function storeCurrency(SupportedCurrencies $code): Currency
    {
        $currency = Currency::create(CurrencyCode::from($code->value));

        $this->currencyRepository->store($currency);

        return $currency;
    }

    public function fetchWalletById(WalletId $walletId): Wallet
    {
        return $this->walletRepository->fetchById($walletId);
    }

    /**
     * @param WalletOwner[] $owners
     */
    public function storeWallet(array $owners, WalletCurrency $walletCurrency): Wallet
    {
        $wallet = WalletMother::create($owners, $walletCurrency);

        $this->walletRepository->store($wallet);

        return $wallet;
    }

    /**
     * @return TransactionReadModel[]
     */
    public function fetchTransactionsByWalletAndOwner(
        WalletOwnerExternalId $ownerExternalId,
        WalletId $walletId,
        int $perPage = 100,
        int $page = 1
    ): array {
        /** @var TransactionReadModel[] $transactions */
        $transactions = $this->transactionReadModel->fetchByWallet($ownerExternalId, $walletId, $perPage, $page)->items;

        return $transactions;
    }

    /**
     * @return Transaction[]
     */
    public function fetchTransactionsByWallet(WalletId $walletId, WalletCurrency $currency): array
    {
        return $this->transactionRepository->fetchTransactionsByWallet($walletId, $currency);
    }

    public function storeTransaction(Transaction $transaction): void
    {
        $this->transactionRepository->store($transaction);
    }
}
