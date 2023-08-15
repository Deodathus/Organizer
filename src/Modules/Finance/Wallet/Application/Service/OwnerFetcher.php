<?php

declare(strict_types=1);

namespace App\Modules\Finance\Wallet\Application\Service;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Modules\Finance\Wallet\Application\Exception\CannotFindWalletCreatorIdentityException;
use App\Shared\Application\Messenger\QueryBus;

final readonly class OwnerFetcher
{
    public function __construct(
        private QueryBus $queryBus
    ) {
    }

    public function fetchByToken(string $apiToken): UserDTO
    {
        try {
            /** @var UserDTO $user */
            $user = $this->queryBus->handle(
                new FetchUserIdByToken($apiToken)
            );
        } catch (UserDoesNotExist $exception) {
            throw CannotFindWalletCreatorIdentityException::withToken($apiToken);
        }

        return $user;
    }
}
