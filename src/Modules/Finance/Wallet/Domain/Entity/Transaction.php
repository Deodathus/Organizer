<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Entity;

use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Modules\Finance\Wallet\Domain\ValueObject\WalletId;

final class Transaction
{
    private function __construct(
        private readonly TransactionId $id,
        private readonly TransactionExternalId $externalId,
        private readonly WalletId $walletId,
        private readonly TransactionAmount $amount,
        private readonly TransactionType $type
    ) {}

    public static function create(
        TransactionExternalId $externalId,
        WalletId $walletId,
        TransactionAmount $amount,
        TransactionType $type
    ): self {
        return new self(
            TransactionId::generate(),
            $externalId,
            $walletId,
            $amount,
            $type
        );
    }

    public static function reproduce(
        TransactionId $id,
        TransactionExternalId $externalId,
        WalletId $walletId,
        TransactionAmount $amount,
        TransactionType $type
    ): self {
        return new self(
            $id,
            $externalId,
            $walletId,
            $amount,
            $type
        );
    }

    public function getId(): TransactionId
    {
        return $this->id;
    }

    public function getExternalId(): TransactionExternalId
    {
        return $this->externalId;
    }

    public function getWalletId(): WalletId
    {
        return $this->walletId;
    }

    public function getAmount(): TransactionAmount
    {
        return $this->amount;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }
}
