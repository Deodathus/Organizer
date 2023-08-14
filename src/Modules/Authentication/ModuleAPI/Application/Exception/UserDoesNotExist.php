<?php
declare(strict_types=1);

namespace App\Modules\Authentication\ModuleAPI\Application\Exception;

final class UserDoesNotExist extends \Exception
{
    public static function withId(string $userId): self
    {
        return new self(
            sprintf('User with given id does not exist! Given id: "%s"', $userId)
        );
    }

    public static function withToken(string $token): self
    {
        return new self(
            sprintf('User with given token does not exist! Given token: "%s"', $token)
        );
    }
}
