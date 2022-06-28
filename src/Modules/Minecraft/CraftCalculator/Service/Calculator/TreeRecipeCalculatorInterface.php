<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\TreeCalculatorResult;
use App\Modules\Minecraft\CraftCalculator\Model\CalculableInterface;

interface TreeRecipeCalculatorInterface
{
    public function calculate(CalculableInterface $calculable, int $amount): TreeCalculatorResult;
}
