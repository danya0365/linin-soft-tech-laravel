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
final class WorkerOperationStatus extends Enum
{
    const PickUp = 'pickup';
    const Wash =   'wash';
    const Dry = 'dry';
    const Iron = 'iron';
    const Packing = 'packing';
    const Collect = 'collect';
    const Close = 'close';
}
