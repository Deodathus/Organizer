<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Adapter;

use App\Modules\Google\YouTube\Entity\ClientAccessToken;
use App\Modules\Google\YouTube\Entity\ClientConfiguration;

interface YouTubeClientInterface
{
    public function getUrlToAccessTokenGeneration(ClientConfiguration $clientConfiguration): string;

    public function getClient(ClientConfiguration $clientConfiguration, ClientAccessToken $accessToken): Client;
}
