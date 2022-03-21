# LininSoftTech!

ซอฟท์แวร์จัดการบันทึกประวัติการทำงานของพนักงานในแต่ละวัน รวมทั้งบันทึกข้อมูลวัตถุดิบและพลังงานทั้งหมดที่ใช้ด้วย และสามารถดูข้อมูลสถิติทั้งหมดที่เกิดขึ้นกับพนักงาน วัตถุดิบ และพลังงาน


# Sitemap

1.  สินค้า (2 หน้า 6,000 บาท)
	- ผ้าทั่วไป
	- ผ้าแก้ไข
2.  ลูกค้า (2 หน้า 9,000 บาท)
	- ใส่ข้อมูลผ้าสะอาดและจำนวนเงิน
3.  ปฏิบัติการ (รวม 37 หน้า 111,000 บาท)
	- รับสินค้า (2 หน้า)
	- ซัก (7 หน้า)
	- อบ (7 หน้า)
	- รีด (7 หน้า)
	- พับแพค (7 หน้า)
	- จัดเก็บ (7 หน้า)
4.  พลังงาน (7 หน้า 21,000 บาท)
	- บันทึกพลังงานที่ใช้
5.  พนักงาน (3 หน้า 9,000 บาท)
	-  เลือกแผนก
		- เลือกพนักงาน
			- กรองวันที่ข้อมูล Jobs ที่ทำได้
			- แสดงกราฟจำนวนผ้าที่ทำได้ในแต่ละ ชม ของทุกวันรวมกัน
6.  วิเคราะห์ (รอคิดราคาช่วงสุดท้าย เพราะต้องรอให้ข้อมูลหน้าอื่นๆเสร็จเรียบร้อยก่อน)
7.  สต๊อก (3 หน้า 9,000 บาท)
	- เลือกประเภทของวัตถุดิบ
		- วัตถุดิบ
8.  ตั้งค่า (15 หน้า 45,000 บาท)

# Database
Database ทั้งหมด (19,000 บาท)

1.	customers
2.	customer_billing_payment_logs
3.	jobs
4.	job_groups
5.	job_group_activity_logs
6.	job_activity_logs
7.	employee_operation_logs
8.	energy_resources
9.	energy_resource_logs
10.	departments
11.	employees
12.	inventory_groups
13.	inventories
14.	inventory_stock_logs
15.	washing_machines
16.	dryer_machines
17.	linen_types
18. linen_products
19.	users

# Project Budget

ในเรตราคาปกติ คิดราคาหน้าละ 3,000 บาท

 - 8 เมนู มีทั้งหมด 69 หน้า รวมเป็น 207,000 บาท
 - 19 table database รวมเป็นเงิน 19,000 บาท
 
***รวม 226,000 บาท ตีกลมๆ 220,000 บาท***
> อาจจะแยกคิดเงินส่งงานเป็นเมนู ไม่ต้องจ่ายทีเดียว

เนื่องจากได้ตกลงกับลูกค้าใช้ UI แบบธรรมดา ไม่ต้องมีอะไรพิเศษมาก เราจะคิดแค่หน้าละ 1,500 บาท

 - 8 เมนู มีทั้งหมด 69 หน้า รวมเป็น 102,000 บาท
 - 19 table database รวมเป็นเงิน 19,000 บาท

***รวม 121,000 บาท ตีกลมๆ 120,000 บาท***

> ค่าใช้จ่ายยังไม่รวมหน้า วิเคราะห์ข้อมูลที่อาจจะมีการเพิ่มหรือลดในภายหลัง เพราะหน้าวิเคราะห์ต้องมีการทำหลังจากเว็บถูกใช้งานไปแล้ว 1-3 เดือน

## สินค้า
แสดงรายการ

 1. ผ้าทั่วไป
 2. ผ้าแก้ไข

เมื่อกดเข้าไปจะแสดงรายการจาก  table `jobs`

 1. มีฟิลเตอร์กรองข้อมูลตามวันที่
 2. มีฟิลเตอร์กรองตามชนิดผ้า table `linen_types`
 3. มี Sort Order
 4. แสดง Summary ของ Table สรุปยอด
	 1. จำนวนชิ้น
	 2. น้ำหนัก
 5. ไฮไลต์ข้อความ Text Color ตามสีของผ้า

## ลูกค้า
แสดงรายการลูกค้า
สร้าง Database table `customers`
```{ fields: { id, name, total_wet_weight, total_dry_weight, total_billing_weight, total_edit_weight, total_billing_payment } }```

พร้อมกับ sum ข้อมูลจาก  `table jobs`
มี column ยอดรวมน้ำหนักผ้าเปียก, ยอดรวมผ้าสะอาด, ยอดรวมน้ำหนักแก้ไข

 1. โรงพยาบาล A
 2. โรงพยาบาล B
 3. โรงพยาบาล C
 4. ...

เมื่อเลือกแล้ว ให้แสดงรายการ `table jobs` ของลูกค้านั้นๆ
- ป้อนข้อมูลผ้าสะอาด และจำนวนเงิน ลง `table jobs`

สร้าง Database table `customer_billing_payment_logs`
```{ fields: { id, customer_id, dry_weight, payment_paid } }```

## ปฏิบัติการ

 1. รับสินค้า (สร้าง Job Group)
สร้าง Database table `job_groups`

```{ fields: { id, customer_id, wet_weight, employee_id, dry_weight, total_pieces, status = operation_status(progress, packing, collect) } }```

สร้าง Database table `job_group_activity_logs`

```{ fields: { id, job_group_id, employee_id, log_type = enum(status, dry_weight, total_pieces), old_value, new_value } }```
	 1. เลือกชื่อลูกค้า
	 2. ใส่จำนวนน้ำหนัก
 1. ซัก (สร้าง Job ID Status = ซัก)
 สร้าง Database table `jobs`

 ```{ fields: { id, job_group_id {customer_id, weight}, employee_id, job_type = enum(new, edit), washing_machine_id, dryer_machine_id, laundry_type_id, laundry_product_id, wet_weight, color, operation_status = enum('wash', 'dry', 'iron') }```

 สร้าง Database table `job_activity_logs`

```{ fields: { id, job_id, employee_id, log_type = enum(status, washing_machine_id, dryer_machine_id, laundry_type_id, laundry_product_id, wet_weight, color), old_value, new_value } }```

สร้าง Database table `employee_operation_logs`

```{ fields: { id, employee_id, operation_type = enum('wash', 'dry', 'iron', 'packing', 'collect') action_type = enum(start, stop) } }```
	 1. ต้องเลือกพนังงานคนที่เบิกพร้อมใส่ Password ส่วนตัวเพื่อป้องกันการแกล้ง
	 2. ผ้าทั่วไป ผ้าแก้ไข
	 3. เครื่องซักผ้า (เลือกตามน้ำหนักผ้า)
	 4. เลือกกองผ้าจากลูกค้าที่รับมาช่วงเช้า เช่น กองผ้า Customer A ขนาด 100กก. (เลือก Job Group)
	 5. ชนิดผ้า (ผ้าขน)
	 6. ผ้าเช็ดมือ
	 7. ใส่จำนวนกี่โลที่หยิบจากกองผ้าลูกค้า (ต้องไม่เกิน 100กก และไม่เกินขนาดตความจุ เครื่องซักผ้า)
	 8. เลือกสี 
	 9. ยืนยัน 
	 10. เพิ่ม log ลง table `job_activity_logs`
	 11. เพิ่ม log ลง table `employee_operation_logs`
 1. อบ  (เลือก Job ID Status = ซัก เปลี่ยนเป็น Status = อบ)
	 12. เลือก Job ทำงานต่อจาก การซัก แต่เปลี่ยนพนักงาน ขั้นตอนคล้ายกันหมด แต่ต้องเลือก dryer_machines
	 13. เพิ่ม log ลง table `job_activity_logs`
	 14. เพิ่ม log ลง table `employee_operation_logs`
 2. รีด (เลือก Job Status = อบ เปลี่ยนเป็น Status = รีด)
	 15. ถ้าเลือก Job (ผ้า 20กก จาก  100กก) ทำงานต่อจากการ อบ แต่เปลี่ยนพนักงาน นับจำนวนผ้า แทนนับตามน้ำหนัก
	 16. เพิ่ม log ลง table `job_activity_logs`
	 17. เพิ่ม log ลง table `employee_operation_logs`
 3. พับแพ็ค (เลือก Job Group Set Status = packing)
	 18. ถ้าเลือก Job Group (หยิบผ้าที่รีดแล้ว จาก 100กก) จะเป็นการเอาผ้าที่รีด ทั้งหมดจาก Job Group มาพับแพค ใส่จำนวนชิ้น
	 19. เพิ่ม log ลง table `job_group_activity_logs`
	 20. เพิ่ม log ลง table `employee_operation_logs`
 4. จัดเก็บ (เลือก Job Group Set Status = collect)
	 21. เลือกผ้าจาก Job Group แล้วเอาไปชั่งน้ำหนักตอนแห้ง
	 22. เพิ่ม log ลง table `job_group_activity_logs`
	 23. เพิ่ม log ลง table `employee_operation_logs`

## พลังงาน
บันทึกประวัติการใช้งานพลังงานในแต่ละวันหรือสัปดาห์หรือเดือนตามตกลง
สร้าง Database table `energy_resources`
```{ fields: { id, name } }```	

สร้าง Database table `energy_resource_logs`
```{ fields: { id, energy_resource_id, value, unit, lot_number } }```	

แสดงรายการพลังงาน และสร้างหน้ารายการบันทึกในแต่ละหน้าแบบละเอียด

 1. น้ำ
 2. ไฟฟ้า
 3. แก็ส
 4. ชีวมวล
 5. น้ำมันเตา
 6. ประวัติ
	 1. แสดงประวัติการบันทึกการใช้งาน

กดเข้าไปในแต่ละเมนูพลังงาน แล้วบันทึกข้อมูลลง table `energy_resource_logs`

## พนักงาน
แสดงรายการแผนกของพนักงาน
สร้าง Database table `departments` (ซัก, อบ, รีด, พับแพ็ค, จัดเก็บ)
```{ fields: { id, name } }```

เลือกแผนกแล้ว แสดงรายการพนักงาน
สร้าง Database table `employees`
```{ fields: { id, name, photo, department_id} }```

เลือกพนักงาน

 1. กรองวันที่ข้อมูล Jobs ที่ทำได้
 2. แสดงกราฟจำนวนผ้าที่ทำได้ในแต่ละ ชม ของทุกวันรวมกัน

## วิเคราะห์
แสดงสถิติอย่างละเอียด 
					
## สต๊อก

สร้าง Database table `inventory_groups`
```{ fields: { id, name } }```	
แสดงรายการกลุ่มต้นทุนการผลิต  

 1. เคมี/ผงซักฟอก
 2. ถุงพลาสติก
 3. วัสดุทั่วไป
 4. ...

เมื่อกดเข้าไปในแต่ละเมนู จะเจอไอเท็มย่อยของเมนูนั้นๆ
สร้าง Database table `inventories`
```{ fields: { id, name, inventory_group_id, unit, total_quantity, remain_quantity } }```	

สร้าง Database table `inventory_stock_logs`
```{ fields: { id, inventory_id, quantity, type = export, import } }```	

ต้องเลือกพนักงานคนที่เบิกพร้อมใส่ Password ส่วนตัวเพื่อป้องกันการแกล้ง

 1. สามารถเพิ่มหรือลบได้
 2. สามารถเพิ่มหรือลบจำนวนได้
 3. บันทึกประวัติการเพิ่มหรือลดจำนวน

## ตั้งค่า

 1. เครื่องซักผ้า
สร้าง Database table `washing_machines`
```{ fields: { id, name, photo, maximum_weight} }```
 2. เครื่องอบ
สร้าง Database table `dryer_machines`
```{ fields: { id, name, photo, maximum_weight} }```
 3. ชนิดผ้า
 สร้าง Database table `linen_types`
```{ fields: { id, name } }```
 4. ผ้า
สร้าง Database table `linen_products`
```{ fields: { id, name, linen_type_id} }```
 5. จัดการพนักงาน table `employees`

> **Note** ทุกครั้งที่เลือกพนักงานเพื่อดำเนินการ จะต้อง Password ส่วนตัวเพื่อป้องกันการแกล้ง