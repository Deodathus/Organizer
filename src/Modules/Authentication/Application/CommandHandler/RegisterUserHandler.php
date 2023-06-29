<?php
declare(strict_types=1);

namespace App\Modules\Authentication\Application\CommandHandler;

use App\Modules\Authentication\Application\Command\RegisterUser;
use App\Modules\Authentication\Application\Service\ExternalUserRepository;
use App\Modules\Authentication\Domain\Entity\User;
use App\Modules\Authentication\Domain\Repository\UserRepository;
use App\Modules\Authentication\Domain\ValueObject\ExternalUserId;
use App\Modules\Authentication\Domain\ValueObject\Token;
use App\Modules\Authentication\Domain\ValueObject\UserName;
use App\Shared\Application\Messenger\CommandHandler;

final class RegisterUserHandler implements CommandHandler
{
    public function __construct(
        private readonly ExternalUserRepository $externalUserRepository,
        private readonly UserRepository $userRepository
    ) {}

    public function __invoke(RegisterUser $registerUser): void
    {
        $externalUser = $this->externalUserRepository->fetchById($registerUser->id);

        $this->userRepository->register(
            User::register(
                ExternalUserId::fromString($externalUser->externalId),
                new Token($externalUser->token),
                new UserName('New', 'bee')
            )
        );
    }
}
