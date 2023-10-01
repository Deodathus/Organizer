<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\ViewModel;

use App\Modules\Finance\Wallet\Domain\Entity\Wallet;

final readonly class WalletViewModel
{
    public function __construct(
        public string $id,
        public string $name,
        public string $balance,
        public string $currencyCode
    ) {
    }

    public static function fromEntity(Wallet $wallet): self
    {
        return new self(
            $wallet->getId()->toString(),
            $wallet->getName(),
            number_format(((int) $wallet->getBalance()->toString()) / 100),
            $wallet->getCurrencyCode()
        );
    }

    /**
     * @return array{id: string, name: string, balance: string, currencyCode: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'balance' => $this->balance,
            'currencyCode' => $this->currencyCode,
        ];
    }
}
