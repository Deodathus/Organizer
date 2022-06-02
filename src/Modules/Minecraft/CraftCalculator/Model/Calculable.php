<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Model;

use App\Modules\Minecraft\Item\Entity\RecipeInterface;
use Doctrine\Common\Collections\Collection;

final class Calculable implements CalculableInterface
{
    public function __construct(private readonly RecipeInterface $recipe) {}

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
