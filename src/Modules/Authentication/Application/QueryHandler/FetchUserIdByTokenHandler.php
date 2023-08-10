<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\QueryHandler;

use App\Modules\Authentication\Domain\Exception\UserDoesNotExist;
use App\Modules\Authentication\Domain\Repository\UserRepository;
use App\Modules\Authentication\Domain\ValueObject\Token;
use App\Modules\Authentication\ModuleAPI\Application\DTO\UserDTO;
use App\Modules\Authentication\ModuleAPI\Application\Exception\UserDoesNotExist as UserDoesNotExistApiException;
use App\Modules\Authentication\ModuleAPI\Application\Query\FetchUserIdByToken;
use App\Shared\Application\Messenger\QueryHandler;

final readonly class FetchUserIdByTokenHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function __invoke(FetchUserIdByToken $query): UserDTO
    {
        try {
            $user = $this->repository->fetchByToken(new Token($query->apiToken));
        } catch (UserDoesNotExist $exception) {
            throw UserDoesNotExistApiException::withToken($query->apiToken);
        }

        return new UserDTO($user->getUserId()->toString());
    }
}
