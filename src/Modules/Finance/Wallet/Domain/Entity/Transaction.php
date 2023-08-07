<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Domain\Entity;

use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class Transaction
{
    private const WITHDRAW_TRANSACTION_TYPES = [
        TransactionType::WITHDRAW,
        TransactionType::EXPENSE,
        TransactionType::TRANSFER_CHARGE,
    ];

    private function __construct(
        private TransactionId $id,
        private WalletId $walletId,
        private TransactionAmount $amount,
        private TransactionType $type,
        private ?TransactionExternalId $externalId = null
    ) {}

    public static function create(
        WalletId $walletId,
        TransactionAmount $amount,
        TransactionType $type,
        TransactionExternalId $externalId = null
    ): self {
        return new self(
            TransactionId::generate(),
            $walletId,
            $amount,
            $type,
            $externalId
        );
    }

    public static function reproduce(
        TransactionId $id,
        WalletId $walletId,
        TransactionAmount $amount,
        TransactionType $type,
        TransactionExternalId $externalId = null
    ): self {
        return new self(
            $id,
            $walletId,
            $amount,
            $type,
            $externalId
        );
    }

    public function getId(): TransactionId
    {
        return $this->id;
    }

    public function getExternalId(): ?TransactionExternalId
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

    public function isWithdrawType(): bool
    {
        return in_array($this->type, self::WITHDRAW_TRANSACTION_TYPES, true);
    }
}
