<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Adapter\Calculator\Model;

use App\Modules\Minecraft\CraftCalculator\Model\CalculableInterface;
use App\Modules\Minecraft\Item\Entity\Recipe;
use Doctrine\Common\Collections\Collection;

final class Calculable implements CalculableInterface
{
    public function __construct(
        private readonly Recipe $recipe
    ) {
    }

    public function getId(): int
    {
        return $this->recipe->getId();
    }

    public function getIngredients(): Collection
    {
        return $this->recipe->getIngredients();
    }

    public function getResults(): Collection
    {
        return $this->recipe->getResults();
    }
}
