<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\TreeCalculatorResult;
use App\Modules\Minecraft\CraftCalculator\Entity\Calculable;

interface TreeRecipeCalculatorInterface
{
    public function calculate(Calculable $calculable, int $amount): TreeCalculatorResult;
}
