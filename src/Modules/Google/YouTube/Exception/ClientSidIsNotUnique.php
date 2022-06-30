<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Exception;

use Exception;
use Throwable;

final class ClientSidIsNotUnique extends Exception
{
    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
