<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Adapter;

use Google\Client as GoogleClient;

final class Client
{
    public function __construct(
        private readonly GoogleClient $client
    ) {}

    public function getClient(): GoogleClient
    {
        return $this->client;
    }
}
