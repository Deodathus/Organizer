<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Service;

use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyCodeIsNotSupportedException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyDoesNotExist;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCreatorDoesNotOwnWallet;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCurrencyIsDifferentWalletHas;
use App\Modules\Finance\Wallet\Domain\Exception\WalletBalanceIsNotEnoughToProceedTransaction;
use App\Modules\Finance\Wallet\Domain\Exception\WalletDoesNotExist;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator as TransactionCreatorId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class TransactionRegistrar
{
    public function __construct(
        private CurrencyFetcher $currencyFetcher,
        private WalletRepository $walletRepository,
        private TransactionRepository $transactionRepository
    ) {}

    /**
     * @throws WalletDoesNotExistException
     * @throws CurrencyCodeIsNotSupportedException
     * @throws CurrencyDoesNotExist
     * @throws TransactionCreatorDoesNotOwnWallet
     * @throws TransactionCurrencyIsDifferentWalletHas
     * @throws WalletBalanceIsNotEnoughToProceedTransaction
     */
    public function register(
        TransactionType $type,
        WalletId $walletId,
        TransactionAmount $amount,
        TransactionCreator $transactionCreator,
        ?TransactionExternalId $externalId = null
    ): void {
        $this->currencyFetcher->fetch($amount->value->getCurrency()->getCode());

        try {
            $wallet = $this->walletRepository->fetchById($walletId);
        } catch (WalletDoesNotExist $exception) {
            throw WalletDoesNotExistException::withId($walletId->toString());
        }

        $transaction = Transaction::create(
            $walletId,
            $amount,
            $type,
            $externalId
        );

        $wallet->registerTransaction(
            TransactionCreatorId::fromString($transactionCreator->externalId),
            $transaction
        );

        $this->transactionRepository->store($transaction);
    }
}
