<?php

use App\Enums\OperationType;

return [

    OperationType::class => [
        OperationType::Wash => "ซัก - Wash",
        OperationType::Dry => "อบ - Dry",
        OperationType::Iron => "รีด - Iron",
        OperationType::Packing => "พับแพ็ค - Packing",
        OperationType::Collect => "จัดเก็บ - Collect",
    ],
];
