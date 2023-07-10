<?php
declare(strict_types=1);

namespace App\Tests\Modules\Authentication\Application\CommandHandler;

use App\Modules\Authentication\Application\Command\RegisterUser;
use App\Modules\Authentication\Application\CommandHandler\RegisterUserHandler;
use App\Modules\Authentication\Domain\ValueObject\UserExternalId;
use App\Tests\Modules\Authentication\TestDoubles\ExternalUserRepositoryFake;
use App\Tests\Modules\Authentication\TestDoubles\UserRepositoryFake;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class RegisterUserHandlerTest extends TestCase
{
    public function testShouldRegisterUser(): void
    {
        $externalRepository = new ExternalUserRepositoryFake();
        $userRepository = new UserRepositoryFake();
        $sut = new RegisterUserHandler($externalRepository, $userRepository);

        $externalUserId = Uuid::uuid4();
        ($sut)(new RegisterUser($externalUserId->toString()));

        $registeredUser = $userRepository->fetchByExternalId(UserExternalId::fromString($externalUserId->toString()));
        $this->assertSame($externalUserId->toString(), $registeredUser->getExternalUserId()->toString());
    }
}
