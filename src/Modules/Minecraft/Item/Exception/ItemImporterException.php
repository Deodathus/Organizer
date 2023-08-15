<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Exception;

use Exception;
use JetBrains\PhpStorm\Pure;
use Throwable;

class ItemImporterException extends Exception
{
    #[Pure]
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function fromException(Throwable $e): self
    {
        return new self(
            $e->getMessage(),
            $e
        );
    }
}
