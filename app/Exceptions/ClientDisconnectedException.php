<?php

namespace App\Exceptions;

/**
 * โยนจาก emit callback เมื่อ client ตัดการเชื่อมต่อ (กดหยุด/ปิดหน้า)
 * เพื่อหยุด streaming tool loop และปิด upstream stream
 */
class ClientDisconnectedException extends \RuntimeException
{
}
