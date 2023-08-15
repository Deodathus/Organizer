<?php

declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Shared\Application\Messenger\QueryHandler;

final class FetchUserIdByTokenThrowingExceptionHandlerStub implements QueryHandler
{
    public function __construct(
        private readonly \Exception $exceptionToBeThrown
    ) {
    }

    public function __invoke(FetchUserIdByToken $query): UserDTO
    {
        throw $this->exceptionToBeThrown;
    }
}
