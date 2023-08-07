<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\TestUtils;

use App\Modules\Finance\Currency\Domain\Entity\Currency;
use App\Modules\Finance\Currency\Domain\Repository\CurrencyRepository;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Infrastructure\Repository\WalletRepository;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class WalletService
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
        private WalletRepository $walletRepository
    ) {}

    public function storeCurrency(CurrencyCode $code): Currency
    {
        $currency = Currency::create($code);

        $this->currencyRepository->store($currency);

        return $currency;
    }

    public function fetchWalletById(WalletId $walletId): Wallet
    {
        return $this->walletRepository->fetchById($walletId);
    }
}
