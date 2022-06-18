<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Admin()
 * @method static static Manager()
 * @method static static Supervisor()
 * @method static static Employee()
 */
final class UserRole extends Enum
{
    const Admin = 'admin';
    const Manager = 'manager';
    const Supervisor = 'supervisor';
    const Customer = 'customer';
    const Employee = 'employee';
}
