<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Application\Exception;

final class UserDoesNotExistException extends \Exception
{
    public static function withToken(string $token): self
    {
        return new self(sprintf('Token: "%s"', $token));
    }

    public static function withExternalId(string $externalId): self
    {
        return new self(sprintf('External id: "%s"', $externalId));
    }
}
