<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\ViewModel;

use App\Modules\Finance\Wallet\Domain\Entity\Transaction;

final readonly class TransactionViewModel
{
    public function __construct(
        public string $id,
        public string $walletId,
        public string $amount,
        public string $type,
        public string $creatorId,
        public ?string $externalId = null
    ) {
    }

    public static function fromEntity(Transaction $transaction): self
    {
        return new self(
            $transaction->getId()->toString(),
            $transaction->getWalletId()->toString(),
            $transaction->getAmount()->toString(),
            $transaction->getType()->value,
            $transaction->getTransactionCreator()->toString(),
            $transaction->getExternalId()?->toString()
        );
    }
}
