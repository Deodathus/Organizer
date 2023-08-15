<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Adapter\Calculator;

use App\Modules\Minecraft\Item\DTO\Recipe\Calculated\CalculatorResult;
use App\Modules\Minecraft\Item\DTO\Recipe\CalculatedTree\TreeCalculatorResult;
use App\Modules\Minecraft\Item\Entity\Recipe;

interface CalculatorAdapterInterface
{
    public function calculate(Recipe $recipe, int $amount): CalculatorResult;

    public function calculateWithTree(Recipe $recipe, int $amount): TreeCalculatorResult;
}
