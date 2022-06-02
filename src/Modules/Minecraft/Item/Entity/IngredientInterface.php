<?php

namespace App\Modules\Minecraft\Item\Entity;

interface IngredientInterface
{
    public function getId(): int;

    public function getAmount(): int;

    public function getItemId(): int;

    public function getItemName(): string;

    public function getAsRecipeResult(): array;
}
