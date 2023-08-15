<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Service;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Wallet\Application\DTO\TransactionCreator;
use App\Modules\Finance\Wallet\Application\Exception\CannotRegisterTransferTransactionWithoutReceiverWalletIdException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyCodeIsNotSupportedException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotExistException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCreatorDoesNotOwnWalletException;
use App\Modules\Finance\Wallet\Application\Exception\TransactionCurrencyIsDifferentWalletHasException;
use App\Modules\Finance\Wallet\Application\Exception\WalletBalanceIsNotEnoughToProceedTransactionException;
use App\Modules\Finance\Wallet\Application\Exception\WalletDoesNotExistException;
use App\Modules\Finance\Wallet\Domain\Entity\Transaction;
use App\Modules\Finance\Wallet\Domain\Entity\Wallet;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCreatorDoesNotOwnWallet;
use App\Modules\Finance\Wallet\Domain\Exception\TransactionCurrencyIsDifferentWalletHas;
use App\Modules\Finance\Wallet\Domain\Exception\WalletBalanceIsNotEnoughToProceedTransaction;
use App\Modules\Finance\Wallet\Domain\Exception\WalletDoesNotExist;
use App\Modules\Finance\Wallet\Domain\Repository\TransactionRepository;
use App\Modules\Finance\Wallet\Domain\Repository\WalletRepository;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionAmount;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionCreator as TransactionCreatorId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionExternalId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionReceiverWalletId;
use App\Modules\Finance\Wallet\Domain\ValueObject\TransactionType;
use App\Shared\Application\Messenger\QueryBus;
use App\Shared\Domain\ValueObject\WalletId;

final readonly class TransactionRegistrar
{
    public function __construct(
        private CurrencyFetcher $currencyFetcher,
        private WalletRepository $walletRepository,
        private TransactionRepository $transactionRepository,
        private QueryBus $queryBus
    ) {
    }

    /**
     * @throws WalletDoesNotExistException
     * @throws CurrencyCodeIsNotSupportedException
     * @throws CurrencyDoesNotExistException
     * @throws TransactionCreatorDoesNotOwnWalletException
     * @throws TransactionCurrencyIsDifferentWalletHasException
     * @throws WalletBalanceIsNotEnoughToProceedTransactionException
     * @throws TransactionCreatorDoesNotExistException
     * @throws CannotRegisterTransferTransactionWithoutReceiverWalletIdException
     */
    public function register(
        TransactionType $type,
        WalletId $walletId,
        TransactionAmount $amount,
        TransactionCreator $transactionCreator,
        ?TransactionExternalId $externalId = null,
        ?TransactionReceiverWalletId $receiverWalletId = null
    ): void {
        $this->currencyFetcher->fetch($amount->value->getCurrency()->getCode());
        $transactionCreatorExternalId = $this->resolveTransactionCreatorExternalId($transactionCreator);

        if ($type === TransactionType::TRANSFER_CHARGE) {
            if ($receiverWalletId === null) {
                throw CannotRegisterTransferTransactionWithoutReceiverWalletIdException::create();
            }

            $receiverWallet = $this->resolveWallet(WalletId::fromString($receiverWalletId->toString()));

            $incomeTransaction = Transaction::create(
                $receiverWallet->getId(),
                $amount,
                TransactionType::TRANSFER_INCOME,
                $transactionCreatorExternalId,
                $externalId
            );

            $this->proceedTransaction($receiverWallet, $incomeTransaction);
            $this->transactionRepository->store($incomeTransaction);
        }

        $wallet = $this->resolveWallet($walletId);

        $transaction = Transaction::create(
            $walletId,
            $amount,
            $type,
            $transactionCreatorExternalId,
            $externalId
        );

        $this->proceedTransaction($wallet, $transaction);

        $this->transactionRepository->store($transaction);
    }

    private function resolveWallet(WalletId $walletId): Wallet
    {
        try {
            return $this->walletRepository->fetchById($walletId);
        } catch (WalletDoesNotExist $exception) {
            throw WalletDoesNotExistException::withId($walletId->toString());
        }
    }

    private function resolveTransactionCreatorExternalId(TransactionCreator $transactionCreator): TransactionCreatorId
    {
        try {
            /** @var UserDTO $user */
            $user = $this->queryBus->handle(new FetchUserIdByToken($transactionCreator->apiToken));

            return TransactionCreatorId::fromString($user->userId);
        } catch (UserDoesNotExist $exception) {
            throw TransactionCreatorDoesNotExistException::withToken($transactionCreator->apiToken);
        }
    }

    /**
     * @throws TransactionCreatorDoesNotOwnWalletException
     * @throws TransactionCurrencyIsDifferentWalletHasException
     * @throws WalletBalanceIsNotEnoughToProceedTransactionException
     */
    private function proceedTransaction(Wallet $wallet, Transaction $transaction): void
    {
        try {
            $wallet->registerTransaction($transaction);
        } catch (TransactionCreatorDoesNotOwnWallet $exception) {
            throw TransactionCreatorDoesNotOwnWalletException::withPrevious($exception);
        } catch (TransactionCurrencyIsDifferentWalletHas $exception) {
            throw TransactionCurrencyIsDifferentWalletHasException::withPrevious($exception);
        } catch (WalletBalanceIsNotEnoughToProceedTransaction $exception) {
            throw WalletBalanceIsNotEnoughToProceedTransactionException::withPrevious($exception);
        }
    }
}
