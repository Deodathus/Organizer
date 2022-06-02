<?php

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\CalculatorResult;
use App\Modules\Minecraft\CraftCalculator\Model\CalculableInterface;

interface CalculatorInterface
{
    public function calculate(CalculableInterface $calculable, int $amount): CalculatorResult;
}
