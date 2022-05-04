<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

final class TreeIngredientDTO
{
    public function __construct(
        private readonly int $amount,
        private readonly int $itemId,
        private readonly array $asResult
    ) {}

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * @return TreeIngredientDTO[]
     */
    public function getAsResult(): array
    {
        return $this->asResult;
    }

    #[ArrayShape(['itemId' => "int", 'amount' => "int", 'asResult' => "array"])]
    #[Pure]
    public function toArray(): array
    {
        $asResult = [];

        foreach ($this->getAsResult() as $result) {
            $asResult[] = $result->toArray();
        }

        return [
            'itemId' => $this->getItemId(),
            'amount' => $this->getAmount(),
            'asResult' => $asResult,
        ];
    }
}