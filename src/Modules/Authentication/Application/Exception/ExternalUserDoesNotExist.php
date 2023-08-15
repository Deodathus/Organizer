<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Application\Exception;

final class ExternalUserDoesNotExist extends \Exception
{
    public static function withId(string $externalId): self
    {
        return new self(sprintf('User external id: %s', $externalId));
    }
}
