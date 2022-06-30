<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Request\YouTubeClient;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class YouTubeClientConfigurationCURequest extends AbstractRequest
{
    private function __construct(
        public readonly string $sid,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $scope
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $requestStack = $request->toArray();

        $sid = $requestStack['sid'] ?? null;
        $clientId = $requestStack['clientId'] ?? null;
        $clientSecret = $requestStack['clientSecret'] ?? null;
        $scope = $requestStack['scope'] ?? null;

        Assert::lazy()
            ->that($sid, 'sid')->notEmpty()->maxLength(255)
            ->that($clientId, 'clientId')->notEmpty()->maxLength(255)
            ->that($clientSecret, 'clientSecret')->notEmpty()->maxLength(255)
            ->that($scope, 'scope')->notEmpty()->maxLength(255)
            ->verifyNow();

        return new self(
            sid: $sid,
            clientId: $clientId,
            clientSecret: $clientSecret,
            scope: $scope
        );
    }

    #[ArrayShape(['clientId' => "string", 'clientSecret' => "string", 'scope' => "string"])]
    public function toArray(): array
    {
        return [
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'scope' => $this->scope,
        ];
    }
}
