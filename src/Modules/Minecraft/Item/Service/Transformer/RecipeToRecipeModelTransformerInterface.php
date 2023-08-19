<?php

declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Transformer;

use App\Modules\Minecraft\Item\Response\Model\RecipeModel;
use App\Modules\Minecraft\Item\Search\PaginatedResult;
use Doctrine\Common\Collections\ArrayCollection;

interface RecipeToRecipeModelTransformerInterface
{
    /**
     * @return ArrayCollection<RecipeModel>
     */
    public function transform(PaginatedResult $recipes): ArrayCollection;
}
