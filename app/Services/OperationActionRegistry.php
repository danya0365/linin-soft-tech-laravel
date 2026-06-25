<?php

namespace App\Services;

/**
 * Registry ของ "งานเดินเอกสาร/ธุรกรรม" ที่ AI สั่งทำผ่านแชทได้ (operational actions)
 * ต่างจาก EntityWriteRegistry (CRUD ตรงๆ) ตรงที่ action มี business logic/side-effect
 * (อัปเดต stock, สร้าง Expense/Income, daily summary) — logic อยู่ใน OperationActionService
 *
 * แต่ละ action: key, label, role(worker|supervisor), params(tuple format), rules(Laravel validation)
 * draft ของ action เก็บด้วย entity_key = "action:<key>" reuse โครงสร้าง AiWriteDraft เดิม
 */
class OperationActionRegistry
{
    public static function all(): array
    {
        return [
            'log_energy' => [
                'label' => 'บันทึกการใช้พลังงาน',
                'role' => 'worker',
                'params' => [
                    'energy_resource_id' => ['integer', 'id ทรัพยากรพลังงาน (เรียก list_entities kind=energy_resources ก่อน)', null, true],
                    'value' => ['number', 'ปริมาณที่ใช้', null, true],
                    'cost' => ['number', 'ค่าใช้จ่าย (บาท)', null, true],
                    'created_at' => ['string', 'วันที่ Y-m-d (ไม่ระบุ=วันนี้)'],
                    'employee_id' => ['integer', 'id พนักงาน (ไม่ระบุก็ได้)'],
                    'lot_number' => ['string', 'เลขล็อต (ไม่ระบุก็ได้)'],
                ],
                'rules' => [
                    'energy_resource_id' => 'required|integer',
                    'value' => 'required|numeric',
                    'cost' => 'required|numeric',
                    'created_at' => 'nullable|date',
                    'employee_id' => 'nullable|integer',
                    'lot_number' => 'nullable|string',
                ],
            ],

            'stock_in' => [
                'label' => 'รับสต๊อกเข้า',
                'role' => 'worker',
                'params' => [
                    'inventory_id' => ['integer', 'id สต๊อก (เรียก search_inventories หรือ get_inventories_by_group ก่อน)', null, true],
                    'quantity' => ['number', 'จำนวนที่รับเข้า', null, true],
                    'created_at' => ['string', 'วันที่ Y-m-d (ไม่ระบุ=วันนี้)'],
                ],
                'rules' => [
                    'inventory_id' => 'required|integer',
                    'quantity' => 'required|numeric|gt:0',
                    'created_at' => 'nullable|date',
                ],
            ],

            'stock_out' => [
                'label' => 'เบิกสต๊อกออก',
                'role' => 'worker',
                'params' => [
                    'inventory_id' => ['integer', 'id สต๊อก (เรียก search_inventories ก่อน)', null, true],
                    'quantity' => ['number', 'จำนวนที่เบิกออก', null, true],
                    'cost' => ['number', 'ต้นทุนรวม (บาท)', null, true],
                    'created_at' => ['string', 'วันที่ Y-m-d (ไม่ระบุ=วันนี้)'],
                ],
                'rules' => [
                    'inventory_id' => 'required|integer',
                    'quantity' => 'required|numeric|gt:0',
                    'cost' => 'required|numeric',
                    'created_at' => 'nullable|date',
                ],
            ],

            'billing' => [
                'label' => 'ออกบิลลูกค้า',
                'role' => 'supervisor',
                'params' => [
                    'customer_id' => ['integer', 'id ลูกค้า (เรียก search_customers ก่อน)', null, true],
                    'total_billing_weight' => ['number', 'น้ำหนักรวมที่เรียกเก็บ (กก.)', null, true],
                    'total_billing_payment' => ['number', 'ยอดเงินที่เรียกเก็บ (บาท)', null, true],
                    'billing_payment_date' => ['string', 'วันที่เก็บเงิน Y-m-d', null, true],
                    'total_edit_weight' => ['number', 'น้ำหนักผ้าแก้ไข (ไม่ระบุ=0)'],
                ],
                'rules' => [
                    'customer_id' => 'required|integer',
                    'total_billing_weight' => 'required|numeric',
                    'total_billing_payment' => 'required|numeric',
                    'billing_payment_date' => 'required|date',
                    'total_edit_weight' => 'nullable|numeric',
                ],
            ],

            'department_expense' => [
                'label' => 'บันทึกค่าใช้จ่ายแผนก',
                'role' => 'supervisor',
                'params' => [
                    'department_id' => ['integer', 'id แผนก (เรียก list_entities kind=departments ก่อน)', null, true],
                    'cost' => ['number', 'ค่าใช้จ่าย (บาท)', null, true],
                    'daily_date' => ['string', 'วันที่ Y-m-d (ไม่ระบุ=วันนี้)'],
                    'message' => ['string', 'หมายเหตุ (ไม่ระบุก็ได้)'],
                ],
                'rules' => [
                    'department_id' => 'required|integer',
                    'cost' => 'required|numeric',
                    'daily_date' => 'nullable|date',
                    'message' => 'nullable|string',
                ],
            ],

            'operation' => [
                'label' => 'สร้างงานผ้า',
                'role' => 'worker',
                'params' => [
                    'operation_type' => ['string', 'ประเภทงาน: wash=ซัก, dry=อบ, iron=รีด, packing=แพ็ค, collect=เก็บ', ['wash', 'dry', 'iron', 'packing', 'collect'], true],
                    'employee_id' => ['integer', 'id พนักงานผู้ทำงาน (เรียก search_employees ก่อน)', null, true],
                    'customer_id' => ['integer', 'id ลูกค้า (เรียก search_customers ก่อน)', null, true],
                    'operation_date' => ['string', 'วันที่ทำงาน Y-m-d (ไม่ระบุ=วันนี้)'],
                    'washing_machine_id' => ['integer', 'id เครื่องซัก (เฉพาะ wash, ไม่ระบุก็ได้)'],
                    'dryer_machine_id' => ['integer', 'id เครื่องอบ (เฉพาะ dry, ไม่ระบุก็ได้)'],
                    'items' => ['array', 'รายการผ้าแต่ละชนิดในงานนี้ (อย่างน้อย 1 รายการ)', null, true, [
                        'linen_product_id' => ['integer', 'id ผลิตภัณฑ์ผ้า (เรียก list_linen_products ก่อน)', null, true],
                        'linen_case' => ['string', 'new=ผ้าเคสใหม่ หรือ edit=ผ้าเคสแก้ไข', ['new', 'edit'], true],
                        'color' => ['string', 'สีผ้า', null, true],
                        'amount' => ['number', 'wash/dry=น้ำหนัก(กก.), iron/packing=จำนวนชิ้น, collect=น้ำหนัก(กก.)', null, true],
                        'collect_pack' => ['integer', 'จำนวนแพ็ค (เฉพาะ collect)'],
                    ]],
                ],
                'rules' => [
                    'operation_type' => 'required|in:wash,dry,iron,packing,collect',
                    'employee_id' => 'required|integer',
                    'customer_id' => 'required|integer',
                    'operation_date' => 'nullable|date',
                    'washing_machine_id' => 'nullable|integer',
                    'dryer_machine_id' => 'nullable|integer',
                    'items' => 'required|array|min:1',
                    'items.*.linen_product_id' => 'required|integer',
                    'items.*.linen_case' => 'required|in:new,edit',
                    'items.*.color' => 'required|string',
                    'items.*.amount' => 'required|numeric',
                    'items.*.collect_pack' => 'nullable|integer',
                ],
            ],
        ];
    }

    public static function get(string $key): ?array
    {
        return static::all()[$key] ?? null;
    }

    public static function keys(): array
    {
        return array_keys(static::all());
    }
}
