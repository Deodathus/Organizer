<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

final readonly class TreeIngredientItemDTO
{
    public function __construct(
        private float $amount,
        private int $itemId,
        private string $itemName,
        private array $asResult
    ) {
    }

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

    /**
     * @return array{itemId: int, itemName: string, amount: int, asResult: array|null}
     */
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
