<?php
declare(strict_types=1);

namespace App\Modules\Authentication\ModuleAPI\Application\Exception;

final class UserDoesNotExist extends \Exception
{
    public static function withId(string $userId): self
    {
        return new self(
            sprintf('User id: "%s"', $userId)
        );
    }

    public static function withToken(string $token): self
    {
        return new self(
            sprintf('User token: "%s"', $token)
        );
    }
}
