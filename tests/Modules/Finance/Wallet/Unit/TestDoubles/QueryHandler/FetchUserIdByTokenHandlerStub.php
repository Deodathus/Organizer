<?php
declare(strict_types=1);

namespace App\Tests\Modules\Finance\Wallet\Unit\TestDoubles\QueryHandler;

use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchUserIdByTokenHandlerStub implements QueryHandler
{
    public function __construct(
        private ?string $userId = null
    ) {}

    public function __invoke(FetchUserIdByToken $query): UserDTO
    {
        if ($this->userId === null) {
            throw UserDoesNotExist::withToken($query->apiToken);
        }

        return new UserDTO($this->userId);
    }
}
