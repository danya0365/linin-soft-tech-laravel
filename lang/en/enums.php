<?php

use App\Enums\OperationType;
use App\Enums\UserRole;

return [

    OperationType::class => [
        OperationType::Wash => "ซัก - Wash",
        OperationType::Dry => "อบ - Dry",
        OperationType::Iron => "รีด - Iron",
        OperationType::Packing => "พับแพ็ค - Packing",
        OperationType::Collect => "จัดเก็บ - Collect",
        OperationType::Deliver => "ขนส่ง - Deliver",
        OperationType::Payment => "Payment",
    ],

    UserRole::class => [
        UserRole::Admin => "ผู้ดูแลระบบ - Admin",
        UserRole::Manager => "ผู้จัดการ - Manager",
        UserRole::Supervisor => "ผู้คุมงาน - Supervisor",
        UserRole::Employee => "พนักงาน - Employee",
    ],
];
