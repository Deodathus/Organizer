<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

use JetBrains\PhpStorm\ArrayShape;

final class TreeIngredientItemDTO
{
    public function __construct(
        private readonly float $amount,
        private readonly int $itemId,
        private readonly string $itemName,
        private readonly array $asResult
    ) {}

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    /**
     * @return TreeIngredientDTO[]
     */
    public function getAsResult(): array
    {
        return $this->asResult;
    }

    #[ArrayShape(['itemId' => "int", 'itemName' => "string", 'amount' => "float", 'asResult' => "array"])]
    public function toArray(): array
    {
        $asResult = [];

        foreach ($this->getAsResult() as $result) {
            $asResult[] = $result->toArray();
        }

        return [
            'itemId' => $this->getItemId(),
            'itemName' => $this->getItemName(),
            'amount' => $this->getAmount(),
            'asResult' => $asResult ?: null,
        ];
    }
}
