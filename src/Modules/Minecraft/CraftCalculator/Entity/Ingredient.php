<?php

namespace App\Modules\Minecraft\CraftCalculator\Entity;

interface Ingredient
{
    public function getAmount(): int;

    public function getItemId(): int;
}
