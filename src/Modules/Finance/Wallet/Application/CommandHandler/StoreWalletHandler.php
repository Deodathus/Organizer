<?php
declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\CommandHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Currency\ModuleAPI\Application\DTO\CurrencyDTO;
use App\Modules\Finance\Currency\ModuleAPI\Application\Enum\SupportedCurrencies;
use App\Modules\Finance\Currency\ModuleAPI\Application\Query\FetchCurrencyByCode;
use App\Modules\Finance\Wallet\Application\Command\StoreWallet;
use App\Modules\Finance\Wallet\Application\DTO\CreatedWallet;
use App\Modules\Finance\Wallet\Application\DTO\WalletDTO;
use App\Modules\Finance\Wallet\Application\Exception\CannotFindWalletCreatorIdentityException;
use App\Modules\Finance\Wallet\Application\Exception\CurrencyCodeIsNotSupportedException;
use App\Modules\Finance\Wallet\Domain\Service\WalletPersisterInterface as WalletPersister;
use App\Shared\Application\Messenger\CommandHandler;
use App\Shared\Application\Messenger\QueryBus;

final class StoreWalletHandler implements CommandHandler
{
    public function __construct(
        private readonly WalletPersister $walletPersister,
        private readonly QueryBus $queryBus
    ) {}

    public function __invoke(StoreWallet $wallet): CreatedWallet
    {
        $currency = $this->resolveCurrencyId($wallet->currencyCode);

        try {
            /** @var UserDTO $creatorId */
            $creatorId = $this->queryBus->handle(
                new FetchUserIdByToken($wallet->creatorApiToken)
            );
        } catch (UserDoesNotExist $exception) {
            throw CannotFindWalletCreatorIdentityException::withToken($wallet->creatorApiToken);
        }

        $createdWalletId =  $this->walletPersister->persist(
            new WalletDTO(
                $wallet->name,
                $creatorId->userId,
                $wallet->startBalance,
                $currency->id,
                $wallet->currencyCode
            )
        );

        return new CreatedWallet($createdWalletId->toString());
    }

    private function resolveCurrencyId(string $code): CurrencyDTO
    {
        $currencyCode = SupportedCurrencies::tryFrom($code);
        if ($currencyCode === null) {
            throw CurrencyCodeIsNotSupportedException::withCode($code);
        }

        /** @var CurrencyDTO $currency */
        return $this->queryBus->handle(
            new FetchCurrencyByCode($currencyCode)
        );
    }
}
