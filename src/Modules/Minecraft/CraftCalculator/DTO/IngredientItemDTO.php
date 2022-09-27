<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\DTO;

final readonly class IngredientItemDTO
{
    public function __construct(
        private int    $id,
        private int    $amount,
        private int    $itemId,
        private string $itemName
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): int
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
     * @return array{id: int, itemId: int, amount: int, itemName: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'itemId' => $this->getItemId(),
            'amount' => $this->getAmount(),
            'itemName' => $this->getItemName(),
        ];
    }
}
