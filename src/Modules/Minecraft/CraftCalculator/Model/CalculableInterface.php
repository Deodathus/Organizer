<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\CraftCalculator\Model;

use App\Modules\Minecraft\Item\Entity\IngredientInterface;
use App\Modules\Minecraft\Item\Entity\RecipeResultInterface;
use Doctrine\Common\Collections\Collection;

interface CalculableInterface
{
    public function getId(): int;

    /**
     * @return Collection<IngredientInterface>
     */
    public function getIngredients(): Collection;

    /**
     * @return Collection<RecipeResultInterface>
     */
    public function getResults(): Collection;
}
