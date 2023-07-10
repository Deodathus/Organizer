<?php
declare(strict_types=1);

namespace App\Modules\Finance\Currency\Domain\Entity;

use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyCode;
use App\Modules\Finance\Currency\Domain\ValueObject\CurrencyId;

final class Currency
{
    private function __construct(
        private readonly CurrencyId $id,
        private readonly CurrencyCode $code
    ) {}

    public static function create(CurrencyCode $code): self
    {
        return new self(
            CurrencyId::generate(),
            $code
        );
    }

    public static function reproduce(CurrencyId $id, CurrencyCode $code): self
    {
        return new self($id, $code);
    }

    public function getId(): CurrencyId
    {
        return $this->id;
    }

    public function getCode(): CurrencyCode
    {
        return $this->code;
    }
}
