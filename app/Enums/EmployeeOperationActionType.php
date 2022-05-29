<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Start()
 * @method static static Progress()
 * @method static static Stop()
 */
final class EmployeeOperationActionType extends Enum
{
    const Start = 'start';
    const Progress = 'progress';
    const Stop = 'stop';
}
