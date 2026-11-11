<?php

/**
 * External API token abilities (Laravel Sanctum personal access tokens).
 *
 * Sanctum treats '*' as full control — tokenCan() returns true for any ability.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Ability definitions
    |--------------------------------------------------------------------------
    */
    'abilities' => [
        'dashboard:read' => 'อ่านข้อมูล dashboard / รายงานสรุป',
        'dashboard:write' => 'เขียน/แก้ข้อมูล dashboard ผ่าน API (สำรองไว้ใช้ภายหลัง)',
        'resources:read' => 'อ่านข้อมูลตารางสำคัญ (เครื่องจักร, งาน, ลูกค้า, ฯลฯ)',
        'resources:write' => 'เขียน/แก้ข้อมูลตารางผ่าน API (สำรองไว้ใช้ภายหลัง)',
        '*' => 'Full control — ทุก ability',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission levels shown in Admin UI
    |--------------------------------------------------------------------------
    |
    | Each level maps to a list of Sanctum abilities stored on the token.
    | Read only รวมทั้ง dashboard + resources เพื่อให้ token เดียวใช้ครบ
    */
    'permission_levels' => [
        'read' => [
            'label' => 'Read only',
            'description' => 'อ่าน dashboard และข้อมูลตารางสำคัญได้',
            'abilities' => ['dashboard:read', 'resources:read'],
        ],
        'read_write' => [
            'label' => 'Read + Write',
            'description' => 'อ่านและเขียนผ่าน API ได้ (write endpoints ทยอยเพิ่ม)',
            'abilities' => [
                'dashboard:read',
                'dashboard:write',
                'resources:read',
                'resources:write',
            ],
        ],
        'full' => [
            'label' => 'Full control',
            'description' => 'สิทธิ์เต็มทุกอย่าง (*)',
            'abilities' => ['*'],
        ],
    ],

    'default_permission_level' => 'read',

];
