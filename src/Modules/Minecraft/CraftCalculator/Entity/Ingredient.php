<?php

namespace App\Modules\Minecraft\CraftCalculator\Entity;

interface Ingredient
{
    public function getId(): int;

    public function getAmount(): int;

    public function getItemId(): int;

    public function getItemName(): string;

    public function getAsRecipeResult(): array;
}
