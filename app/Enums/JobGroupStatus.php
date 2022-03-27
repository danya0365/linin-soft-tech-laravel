<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static PickUp()
 * @method static static Progress()
 * @method static static Packing()
 * @method static static Collect()
 */
final class JobGroupStatus extends Enum
{
    const PickUp = 'pickup';
    const Progress =   'progress';
    const Packing = 'packing';
    const Collect = 'collect';
    const Close = 'close';
}
