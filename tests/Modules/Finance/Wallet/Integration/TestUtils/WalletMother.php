<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Integration\TestUtils;

use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Entity\WalletOwner;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrency;
use App\Modules\Finance\Wallet\Infrastructure\Adapter\WalletBalanceCreator;
use Ramsey\Uuid\Uuid;

final readonly class WalletMother
{
    public const WALLET_START_BALANCE = '100';

    /**
     * @param WalletOwner[] $owners
     */
    public static function create(array $owners, WalletCurrency $walletCurrency): Wallet
    {
        $walletBalanceCreator = new WalletBalanceCreator();

        return Wallet::create(
            Uuid::uuid4()->toString(),
            $owners,
            $walletBalanceCreator->create(self::WALLET_START_BALANCE, $walletCurrency->currencyCode),
            $walletCurrency
        );
    }
}
