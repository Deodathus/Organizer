<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Entity;

use App\Modules\Finance\Wallet\Domain\ValueObject\WalletBalance;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletCurrencyId;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletId;

final class Wallet
{
    /**
     * @param array<WalletOwner> $owners
     */
    private function __construct(
        private readonly WalletId $id,
        private readonly string $name,
        private readonly array $owners,
        private readonly WalletBalance $balance,
        private readonly WalletCurrencyId $currencyId
    ) {}

    public static function create(
        string $name,
        array $owners,
        WalletBalance $startBalance,
        WalletCurrencyId $currencyId
    ): self {
        return new self(
            WalletId::generate(),
            $name,
            $owners,
            $startBalance,
            $currencyId
        );
    }

    public static function reproduce(
        WalletId $id,
        string $name,
        array $owners,
        WalletBalance $currentBalance,
        WalletCurrencyId $currencyId
    ): self {
        return new self(
            $id,
            $name,
            $owners,
            $currentBalance,
            $currencyId
        );
    }

    public function getId(): WalletId
    {
        return $this->id;
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
        return $this->currencyId;
    }
}
