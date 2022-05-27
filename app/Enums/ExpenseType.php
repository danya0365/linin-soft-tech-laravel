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
    const Water = 'water';
    const Electricity = 'electricity';
    const Gas = 'gas';
    const Biomass = 'biomass';
    const FuelOil = 'fuel_oil';
    const Petrol = 'petrol';
    const DepartmentSalary = 'department-salary';
    const Inventory = 'inventory';
}
