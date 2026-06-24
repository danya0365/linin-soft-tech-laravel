<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerGroup;
use App\Models\Department;
use App\Models\DryerMachine;
use App\Models\Employee;
use App\Models\EnergyResource;
use App\Models\InventoryGroup;
use App\Models\LinenProduct;
use App\Models\LinenType;
use App\Models\Truck;
use App\Models\WashingMachine;

/**
 * Registry กลางของ "ชนิดข้อมูลที่ AI สร้างได้" (เฟส 1: master data single-record)
 *
 * เพิ่มเอนทิตีใหม่ = เพิ่ม entry เดียว ไม่ต้องเขียน method ใหม่:
 *   'model' => Eloquent class (ใช้ ::$rules + ::create), 'label' => ชื่อไทย,
 *   'role'  => สิทธิ์ขั้นต่ำ (admin|manager|supervisor|worker),
 *   'fields'=> [name => [type, desc, enum|null, required]] — แปลงเป็น tool params,
 *   'fks'   => [field => Fk model class] — เช็คว่า id มีจริง,
 *   'skip'  => [field] — ตัดออกจาก payload + rules (เช่น photo อัปโหลดผ่านแชทไม่ได้)
 */
class EntityWriteRegistry
{
    public static function all(): array
    {
        return [
            'customer' => [
                'model' => Customer::class,
                'label' => 'ลูกค้า',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อลูกค้า', null, true],
                    'customer_group_id' => ['integer', 'id กลุ่มลูกค้า (เรียก list_entities kind=customer_groups หาก่อน)', null, true],
                ],
                'fks' => ['customer_group_id' => CustomerGroup::class],
            ],

            'customer_group' => [
                'model' => CustomerGroup::class,
                'label' => 'กลุ่มลูกค้า',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อกลุ่มลูกค้า', null, true],
                ],
            ],

            'department' => [
                'model' => Department::class,
                'label' => 'แผนก',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อแผนก', null, true],
                    'input_unit' => ['string', 'หน่วยรับเข้า เช่น กก. หรือ ชิ้น', null, true],
                    'var_name' => ['string', 'รหัสตัวแปร (อังกฤษ ไม่มีช่องว่าง) — ไม่ระบุก็ได้'],
                ],
            ],

            'employee' => [
                'model' => Employee::class,
                'label' => 'พนักงาน',
                'role' => 'admin',
                'fields' => [
                    'code' => ['string', 'รหัสพนักงาน', null, true],
                    'name' => ['string', 'ชื่อพนักงาน', null, true],
                    'department_id' => ['integer', 'id แผนก (เรียก list_entities kind=departments หาก่อน)', null, true],
                ],
                'fks' => ['department_id' => Department::class],
                'skip' => ['photo'],
            ],

            'linen_type' => [
                'model' => LinenType::class,
                'label' => 'ประเภทผ้า',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อประเภทผ้า', null, true],
                ],
            ],

            'linen_product' => [
                'model' => LinenProduct::class,
                'label' => 'ผลิตภัณฑ์ผ้า',
                'role' => 'admin',
                'fields' => [
                    'linen_type_id' => ['integer', 'id ประเภทผ้า (เรียก list_entities kind=linen_types หาก่อน)', null, true],
                    'name' => ['string', 'ชื่อผลิตภัณฑ์ผ้า', null, true],
                ],
                'fks' => ['linen_type_id' => LinenType::class],
            ],

            'inventory_group' => [
                'model' => InventoryGroup::class,
                'label' => 'กลุ่มสต๊อก',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อกลุ่มสต๊อก', null, true],
                ],
            ],

            'energy_resource' => [
                'model' => EnergyResource::class,
                'label' => 'ทรัพยากรพลังงาน',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อทรัพยากรพลังงาน', null, true],
                ],
            ],

            'washing_machine' => [
                'model' => WashingMachine::class,
                'label' => 'เครื่องซัก',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อเครื่องซัก', null, true],
                    'maximum_weight' => ['number', 'น้ำหนักสูงสุดที่รับได้ (กก.)', null, true],
                ],
                'skip' => ['photo'], // $rules บังคับ photo แต่แชทแนบไฟล์ไม่ได้
                'defaults' => ['photo' => ''], // คอลัมน์ photo เป็น NOT NULL — เติมค่าว่าง ไปเพิ่มรูปทีหลังผ่านหน้าจอ
            ],

            'dryer_machine' => [
                'model' => DryerMachine::class,
                'label' => 'เครื่องอบ',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อเครื่องอบ', null, true],
                    'maximum_weight' => ['number', 'น้ำหนักสูงสุดที่รับได้ (กก.)', null, true],
                ],
                'skip' => ['photo'],
                'defaults' => ['photo' => ''],
            ],

            'truck' => [
                'model' => Truck::class,
                'label' => 'รถ',
                'role' => 'admin',
                'fields' => [
                    'name' => ['string', 'ชื่อรถ', null, true],
                    'plate_number' => ['string', 'ทะเบียนรถ — ไม่ระบุก็ได้'],
                ],
                'skip' => ['photo'],
                'defaults' => ['photo' => ''],
            ],
        ];
    }

    public static function get(string $key): ?array
    {
        return static::all()[$key] ?? null;
    }

    /** key ทั้งหมด (ใช้ประกอบ enum ใน tool definition) */
    public static function keys(): array
    {
        return array_keys(static::all());
    }
}
