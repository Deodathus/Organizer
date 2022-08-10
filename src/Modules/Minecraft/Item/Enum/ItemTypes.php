<?php
declare(strict_types=1);

namespace App\Modules\Minecraft\Item\Enum;

enum ItemTypes: string
{
    // change Item, Fluid, FluidCell const value if you will change this
    case ITEM = 'item';
    case FLUID_CELL = 'fluidCell';
    case FLUID = 'fluid';
}
