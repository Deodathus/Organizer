<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\DTO\Recipe\Import\GregTech;

final class GTMachine
{
    /**
     * @param GTRecipeDTO[] $recipes
     */
    public function __construct(
        public readonly string $name,
        public readonly int $generatedEnergyMultiplier,
        public readonly array $recipes
    ) {}
}
