<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Service\Transformer;

use App\Modules\Minecraft\Item\DTO\Recipe\StoreRecipeDTO;

interface ArrayToRecipeTransformerInterface
{
    public function transform(array $data): StoreRecipeDTO;
}
