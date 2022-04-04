<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class OperationStatus extends Enum
{
    const InProgress = 'in-progress';
    const Close = 'close';
}
