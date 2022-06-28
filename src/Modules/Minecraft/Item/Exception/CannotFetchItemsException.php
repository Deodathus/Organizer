<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;

final class CannotFetchItemsException extends Exception
{
    #[Pure]
    public static function fromException(Exception $exception): self
    {
        return new self($exception->getMessage(), 0, $exception);
    }
}
