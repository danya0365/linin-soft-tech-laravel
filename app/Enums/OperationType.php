<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * @method static static Wash()
 * @method static static Dry()
 * @method static static Iron()
 * @method static static Packing()
 * @method static static Collect()
 */
final class OperationType extends Enum implements LocalizedEnum
{
    const Wash = 'wash';
    const Dry = 'dry';
    const Iron = 'iron';
    const Packing = 'packing';
    const Collect = 'collect';
}
