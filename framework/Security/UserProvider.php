<?php

declare(strict_types=1);

namespace Framework\Security;

use App\Modules\Authentication\Application\Exception\ExternalUserDoesNotExist;
use App\Modules\Authentication\Application\Exception\UserDoesNotExist;
use App\Modules\Authentication\Application\Repository\ExternalUserRepository;
use App\Modules\Authentication\Domain\Repository\UserRepository;
use App\Modules\Authentication\Domain\ValueObject\Token;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly ExternalUserRepository $externalUserRepository
    ) {}

    public function refreshUser(UserInterface $user): UserInterface
    {
        throw new \RuntimeException(
            sprintf('This is stateless REST API! User should not be refreshed!')
        );
    }

    public function supportsClass(string $class): bool
    {
        return UserWrapper::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        try {
            $user = $this->repository->fetchByToken(new Token($identifier));

            $this->externalUserRepository->fetchById($user->getExternalUserId()->toString());
        } catch (UserDoesNotExist|ExternalUserDoesNotExist $exception) {
            throw new UserNotFoundException();
        }

        return UserWrapper::createFromUser($user);
    }
}
