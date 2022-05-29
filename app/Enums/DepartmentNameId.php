<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PickUp()
 * @method static static Wash()
 * @method static static Dry()
 * @method static static Iron()
 * @method static static Packing()
 * @method static static Collect()
 */
final class DepartmentNameId extends Enum
{
    const PickUp = 1; // 1, 2, 3, 4, 5, 6 is value of database primary key id map in table `departments`
    const Wash = 2;
    const Dry = 3;
    const Iron = 4;
    const Packing = 5;
    const Collect = 6;
    const Deliver = 7;
}