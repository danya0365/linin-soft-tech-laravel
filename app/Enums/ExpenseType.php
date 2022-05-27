<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Water()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ExpenseType extends Enum
{
    const Water = 0;
    const Electricity =   1;
    const Gas = 2;
    const Biomass = 3;
    const FuelOil = 4;
    const Petrol = 5;
}
