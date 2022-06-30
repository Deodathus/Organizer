<?php
declare(strict_types=1);

namespace App\Modules\Google\YouTube\Request\YouTubeClient;

use App\Request\AbstractRequest;
use Assert\Assert;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Request as ServerRequest;

final class StoreAccessTokenRequest extends AbstractRequest
{
    private function __construct(
        public string $code
    ) {}

    public static function fromRequest(ServerRequest $request): AbstractRequest
    {
        $codeToGenerateAccessCode = $request->query->get('code');

        Assert::lazy()
            ->that($codeToGenerateAccessCode, 'codeToGenerateAccessCode')->notEmpty()->string()
            ->verifyNow();

        return new self($codeToGenerateAccessCode);
    }

    #[ArrayShape(['code' => "string"])]
    public function toArray(): array
    {
        return [
            'code' => $this->code,
        ];
    }
}
