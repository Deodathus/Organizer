<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\ViewModel;

use App\Modules\Finance\Wallet\Domain\Entity\Transaction;

final readonly class TransactionViewModel
{
    private const CREATED_AT_FORMAT = 'd-m H:i';

    public function __construct(
        public string $id,
        public string $walletId,
        public string $amount,
        public string $type,
        public string $creatorId,
        public string $createdAt,
        public ?string $externalId = null,
    ) {
    }

    public static function fromEntity(Transaction $transaction): self
    {
        return new self(
            $transaction->getId()->toString(),
            $transaction->getWalletId()->toString(),
            number_format(
                (
                    (int) $transaction->getAmount()->toString()
                ) / 100,
                2,
                thousands_separator: ', '
            ),
            $transaction->getType()->value,
            $transaction->getTransactionCreator()->toString(),
            $transaction->getCreatedAt()->format(self::CREATED_AT_FORMAT),
            $transaction->getExternalId()?->toString()
        );
    }
}
