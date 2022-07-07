<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Water()
 * @method static static Electricity()
 * @method static static Gas()
 * @method static static Biomass()
 * @method static static FuelOil()
 */
final class EnergyResourceNameId extends Enum
{
    const Water = 1;
    const Electricity = 2;
    const Gas = 3;
    const Biomass = 4;
    const FuelOil = 5;
    const Petrol = 6;
    const Chemical = 7;
}
