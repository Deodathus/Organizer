<?php

declare(strict_types=1);

namespace App\Modules\Authentication\Application\Exception;

final class ExternalUserCannotBeFetched extends \Exception
{
    public static function create(): self
    {
        return new self();
    }
}
