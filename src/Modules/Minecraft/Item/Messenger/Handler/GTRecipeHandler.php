<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Messenger\Handler;

use App\Modules\Minecraft\Item\Messenger\Message\ImportGTRecipe;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GTRecipeHandler
{
    public function __invoke(ImportGTRecipe $importGTRecipe): void
    {

    }
}
