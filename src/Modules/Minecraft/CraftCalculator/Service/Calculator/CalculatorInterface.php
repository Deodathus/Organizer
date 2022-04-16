<?php

namespace App\Modules\Minecraft\CraftCalculator\Service\Calculator;

use App\Modules\Minecraft\CraftCalculator\DTO\CalculatorResult;
use App\Modules\Minecraft\CraftCalculator\Entity\Calculable;

interface CalculatorInterface
{
    public function calculate(Calculable $calculable, int $amount): CalculatorResult;
}