<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Entity;

interface RecipeResult
{
    public function getId(): int;

    public function getAmount(): int;

    public function getItemId(): int;

    public function getItemName(): string;
}
