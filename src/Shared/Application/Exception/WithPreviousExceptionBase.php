<?php

declare(strict_types=1);

namespace App\Shared\Application\Exception;

abstract class WithPreviousExceptionBase extends \Exception
{
    public static function withPrevious(\Throwable $previous): static
    {
        return new static($previous->getMessage(), previous: $previous);
    }
}
