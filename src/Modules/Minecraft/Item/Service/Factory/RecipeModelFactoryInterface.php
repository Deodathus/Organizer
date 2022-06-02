<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Factory;

use App\Modules\Minecraft\Item\Entity\RecipeInterface;
use App\Modules\Minecraft\Item\Response\Model\RecipeModel;

interface RecipeModelFactoryInterface
{
    public function build(RecipeInterface $recipe): RecipeModel;
}
