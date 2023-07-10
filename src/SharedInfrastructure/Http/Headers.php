<?php
declare(strict_types=1);

namespace App\SharedInfrastructure\Http;

enum Headers: string
{
    case AUTH_TOKEN_HEADER = 'X-Auth-Token';
}
