<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\TestUtils;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Infrastructure\Repository\WalletRepository;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class WalletService
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private WalletRepository $walletRepository,
        private TransactionRepository $transactionRepository
    ) {}

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
     * @return Transaction[]
     */
    public function fetchTransactionsByWallet(WalletId $walletId, WalletCurrency $walletCurrency): array
    {
        return $this->transactionRepository->fetchTransactionsByWallet($walletId, $walletCurrency);
    }
}
