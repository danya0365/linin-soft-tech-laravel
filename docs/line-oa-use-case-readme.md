# 📱 LINE OA Use Cases - LinenSoftTech

เอกสารอธิบาย Use Case และ User Flow ของ LINE OA Chatbot สำหรับระบบ LinenSoftTech

---

## 📋 สรุปเมนูทั้งหมด

| เมนู | ความลึก | คำสั่ง/Keyword |
|------|---------|---------------|
| 📊 สรุปวันนี้ | 1 ชั้น (จบทันที) | `สรุปวันนี้`, `summary` |
| 👥 ลูกค้า | 3 ชั้น | `ลูกค้า`, `customer` |
| 📦 สต๊อก | 2 ชั้น | `สต๊อก`, `stock`, `inventory` |
| ⚡ พลังงาน | 2 ชั้น | `พลังงาน`, `energy` |
| 👷 พนักงาน | 3 ชั้น | `พนักงาน`, `employee` |
| ⚙️ เครื่องจักร | 1 ชั้น (จบทันที) | `เครื่องจักร`, `machine` |
| 📈 รายงาน | 2 ชั้น | `รายงาน`, `report` |

---

## 🎬 Use Case Flows

### 1. 📊 สรุปวันนี้ (ลึก 1 ชั้น)

**วัตถุประสงค์:** ดูภาพรวมการทำงานของโรงซักรีดในวันนี้

```mermaid
flowchart TD
    A[User พิมพ์ '📊 สรุปวันนี้'] --> B[Bot แสดง Flex Message]
    B --> C[📅 วันที่<br/>👥 จำนวนลูกค้า<br/>🧺 จำนวนงานวันนี้<br/>⚡ สรุปพลังงานวันนี้]
    C --> D[✅ จบ]
```

**ข้อมูลที่แสดง:**
- วันที่ปัจจุบัน
- จำนวนลูกค้าทั้งหมดในระบบ
- จำนวน Operation ที่สร้างวันนี้
- สรุปการใช้พลังงานวันนี้ (น้ำ/ไฟ/แก็ส)

**Models ที่ใช้:** `Customer`, `Operation`, `EnergyResourceLog`

---

### 2. 👥 ลูกค้า (ลึก 3 ชั้น)

**วัตถุประสงค์:** ค้นหาและดูข้อมูลลูกค้ารายละเอียด

```mermaid
flowchart TD
    A[User พิมพ์ '👥 ลูกค้า'] --> B[Bot แสดง Quick Reply:<br/>เลือกกลุ่มลูกค้า]
    B --> C[User กดเลือก 'โรงพยาบาล']
    C --> D[Bot แสดง Quick Reply:<br/>รายชื่อลูกค้าในกลุ่ม]
    D --> E[User กดเลือก 'รพ.A']
    E --> F[Bot แสดง Flex Message:<br/>ข้อมูลลูกค้า]
    F --> G[🏥 ชื่อ<br/>💧 น้ำหนักเปียก<br/>☀️ น้ำหนักแห้ง<br/>📝 น้ำหนักแก้ไข<br/>💰 ยอดเงินรวม]
    G --> H[✅ จบ]
```

**ขั้นตอน:**
1. **ชั้นที่ 1:** เลือกกลุ่มลูกค้า (เช่น โรงพยาบาล, โรงแรม, อื่นๆ)
2. **ชั้นที่ 2:** เลือกลูกค้าในกลุ่ม (เช่น รพ.A, รพ.B)
3. **ชั้นที่ 3:** แสดงรายละเอียดลูกค้า (น้ำหนัก, ยอดเงิน)

**Models ที่ใช้:** `CustomerGroup`, `Customer`

---

### 3. 📦 สต๊อก (ลึก 2 ชั้น)

**วัตถุประสงค์:** ตรวจสอบยอดคงเหลือวัตถุดิบ

```mermaid
flowchart TD
    A[User พิมพ์ '📦 สต๊อก'] --> B[Bot แสดง Quick Reply:<br/>เลือกประเภทวัตถุดิบ]
    B --> C[User กดเลือก 'เคมี/ผงซักฟอก']
    C --> D[Bot แสดง Flex Message:<br/>รายการวัตถุดิบ + ยอดคงเหลือ]
    D --> E[📊 ผงซักฟอก: 50 kg<br/>📊 น้ำยาปรับผ้านุ่ม: 30 L<br/>⚠️ สารฟอกขาว: 5 kg]
    E --> F[✅ จบ]
```

**ขั้นตอน:**
1. **ชั้นที่ 1:** เลือกกลุ่มวัตถุดิบ (เคมี, ถุงพลาสติก, วัสดุทั่วไป)
2. **ชั้นที่ 2:** แสดงรายการวัตถุดิบ + ยอดคงเหลือ (สีแดงถ้าน้อยกว่า 20%)

**Models ที่ใช้:** `InventoryGroup`, `Inventory`

**หมายเหตุ:** ยอดที่เหลือน้อยกว่า 20% ของทั้งหมดจะแสดงเป็น **สีแดง** ⚠️

---

### 4. ⚡ พลังงาน (ลึก 2 ชั้น)

**วัตถุประสงค์:** ติดตามการใช้พลังงาน

```mermaid
flowchart TD
    A[User พิมพ์ '⚡ พลังงาน'] --> B[Bot แสดง Quick Reply:<br/>เลือกประเภทพลังงาน]
    B --> C[User กดเลือก 'ไฟฟ้า']
    C --> D[Bot แสดง Flex Message:<br/>ประวัติการใช้งาน]
    D --> E[📊 รวม 7 วัน: 1,500 kWh<br/>28/12: 200 kWh<br/>27/12: 220 kWh<br/>...]
    E --> F[✅ จบ]
```

**ขั้นตอน:**
1. **ชั้นที่ 1:** เลือกประเภทพลังงาน (น้ำ, ไฟฟ้า, แก็ส, ชีวมวล, น้ำมันเตา)
2. **ชั้นที่ 2:** แสดงสรุปรวม 7 วัน + ประวัติรายวัน

**Models ที่ใช้:** `EnergyResource`, `EnergyResourceLog`

---

### 5. 👷 พนักงาน (ลึก 3 ชั้น) 🆕

**วัตถุประสงค์:** ดูรายชื่อพนักงานและสถิติการทำงาน

```mermaid
flowchart TD
    A[User พิมพ์ '👷 พนักงาน'] --> B[Bot แสดง Quick Reply:<br/>เลือกแผนก]
    B --> C[User กดเลือก 'แผนกซัก']
    C --> D[Bot แสดง Quick Reply:<br/>รายชื่อพนักงานในแผนก]
    D --> E[User กดเลือก 'นายสมชาย']
    E --> F[Bot แสดง Flex Message:<br/>ข้อมูล + สถิติพนักงาน]
    F --> G[👤 ชื่อ: นายสมชาย<br/>🏢 แผนก: ซัก<br/>---<br/>📅 วันนี้: 15 ครั้ง<br/>📆 สัปดาห์นี้: 75 ครั้ง<br/>📅 เดือนนี้: 280 ครั้ง<br/>---<br/>🧺 ซัก: 10 ครั้ง<br/>☀️ อบ: 5 ครั้ง]
    G --> H[✅ จบ]
```

**ขั้นตอน:**
1. **ชั้นที่ 1:** เลือกแผนก (ซัก, อบ, รีด, พับแพ็ค, จัดเก็บ)
2. **ชั้นที่ 2:** เลือกพนักงานในแผนก
3. **ชั้นที่ 3:** แสดงข้อมูลพนักงาน + สถิติการทำงาน

**ข้อมูลสถิติที่แสดง:**
- จำนวน Operation วันนี้
- จำนวน Operation สัปดาห์นี้
- จำนวน Operation เดือนนี้
- แยกตามประเภทงาน (ซัก/อบ/รีด/พับแพ็ค/จัดเก็บ)

**Models ที่ใช้:** `Department`, `Employee`, `EmployeeOperationLog`

---

### 6. ⚙️ เครื่องจักร (ลึก 1 ชั้น)

**วัตถุประสงค์:** ดูรายการเครื่องจักรทั้งหมด

```mermaid
flowchart TD
    A[User พิมพ์ '⚙️ เครื่องจักร'] --> B[Bot แสดง Flex Message]
    B --> C[🧺 เครื่องซัก<br/>- เครื่อง 1: 100 kg<br/>- เครื่อง 2: 150 kg<br/>---<br/>🌡️ เครื่องอบ<br/>- เครื่อง A: 80 kg<br/>- เครื่อง B: 100 kg]
    C --> D[✅ จบ]
```

**ข้อมูลที่แสดง:**
- รายชื่อเครื่องซัก + ความจุสูงสุด
- รายชื่อเครื่องอบ + ความจุสูงสุด

**Models ที่ใช้:** `WashingMachine`, `DryerMachine`

---

### 7. 📈 รายงาน (ลึก 2 ชั้น) 🆕

**วัตถุประสงค์:** ดูสรุปรายได้ ค่าใช้จ่าย และกำไรขาดทุน (สำหรับ Manager)

```mermaid
flowchart TD
    A[User พิมพ์ '📈 รายงาน'] --> B[Bot แสดง Quick Reply:<br/>เลือกช่วงเวลา]
    B --> C[User กดเลือก 'เดือนนี้']
    C --> D[Bot แสดง Flex Message:<br/>รายงานสรุป]
    D --> E[📅 ช่วงเวลา: เดือนนี้<br/>---<br/>💰 รายได้: 150,000 ฿<br/>💸 ค่าใช้จ่าย: 80,000 ฿<br/>---<br/>📊 กำไร: 70,000 ฿<br/>---<br/>⚡ ค่าพลังงาน: 25,000 ฿]
    E --> F[✅ จบ]
```

**ขั้นตอน:**
1. **ชั้นที่ 1:** เลือกช่วงเวลา (วันนี้, สัปดาห์นี้, เดือนนี้, ทั้งหมด)
2. **ชั้นที่ 2:** แสดงรายงานสรุป

**ข้อมูลที่แสดง:**
- 💰 รายได้รวม (Income)
- 💸 ค่าใช้จ่ายรวม (Expense)
- 📊 กำไร/ขาดทุน (สีเขียวถ้ากำไร, สีแดงถ้าขาดทุน)
- ⚡ ค่าพลังงานรวม

**Models ที่ใช้:** `Income`, `Expense`, `EnergyResourceLog`

---

## 🔄 User Flow Diagram (ภาพรวม)

```mermaid
flowchart LR
    subgraph Main["🏠 เมนูหลัก"]
        A[User พิมพ์ 'เมนู']
    end
    
    subgraph Options["📋 ตัวเลือก"]
        B[📊 สรุปวันนี้]
        C[👥 ลูกค้า]
        D[📦 สต๊อก]
        E[⚡ พลังงาน]
        F[👷 พนักงาน]
        G[⚙️ เครื่องจักร]
        H[📈 รายงาน]
    end
    
    A --> B
    A --> C
    A --> D
    A --> E
    A --> F
    A --> G
    A --> H
    
    B --> B1[Flex Message: สรุป]
    C --> C1[กลุ่ม] --> C2[ลูกค้า] --> C3[รายละเอียด]
    D --> D1[ประเภท] --> D2[ยอดคงเหลือ]
    E --> E1[ประเภท] --> E2[ประวัติ 7 วัน]
    F --> F1[แผนก] --> F2[พนักงาน] --> F3[สถิติ]
    G --> G1[Flex Message: รายการ]
    H --> H1[ช่วงเวลา] --> H2[รายได้/ค่าใช้จ่าย/กำไร]
```

---

## 🛠️ Technical Implementation

### Controller
- **File:** `app/Http/Controllers/Api/LineChatbotController.php`
- **Functions:**
  - `webhook()` - รับ events จาก LINE
  - `handleMessage()` - จัดการข้อความ
  - `handlePostback()` - จัดการ postback (เมื่อ user กด Quick Reply)
  - `sendMainMenu()` - ส่งเมนูหลัก
  - `sendTodaySummary()` - สรุปวันนี้
  - `sendCustomerMenu()` / `showCustomersByGroup()` / `showCustomerDetail()` - เมนูลูกค้า
  - `sendInventoryMenu()` / `showInventoriesByGroup()` - เมนูสต๊อก
  - `sendEnergyMenu()` / `showEnergyLogs()` - เมนูพลังงาน
  - `sendEmployeeMenu()` / `showEmployeesByDepartment()` / `showEmployeeDetail()` - เมนูพนักงาน
  - `sendMachineStatus()` - เมนูเครื่องจักร
  - `sendReportMenu()` / `sendReportSummary()` - เมนูรายงาน 🆕

### Service
- **File:** `app/Services/LineMessagingService.php`
- **Functions:**
  - `verifySignature()` - ยืนยันความถูกต้องของ request
  - `replyMessage()` - ส่งข้อความตอบกลับ
  - `textMessage()` - สร้างข้อความ text
  - `quickReply()` - สร้าง Quick Reply
  - `flexMessage()` - สร้าง Flex Message
  - `bubbleContainer()` - สร้าง Bubble สำหรับ Flex
  - `infoRow()` - สร้างแถวข้อมูล
  - `separator()` - สร้างเส้นแบ่ง

---

## 📱 UI Components

### Quick Reply
ปุ่มเลือกที่แสดงด้านล่างแชท (แสดงเฉพาะ Mobile)

![Quick Reply Example](https://developers.line.biz/assets/img/quickReplyBar.9a39a1c3.png)

### Flex Message
ข้อความที่มี layout สวยงาม แสดงเป็น Card

![Flex Message Example](https://developers.line.biz/assets/img/bubble.d772dd4e.png)

---

## 🔮 อนาคต (Roadmap)

- [ ] เพิ่ม Rich Menu (เมนูรูปภาพด้านล่าง)
- [ ] เพิ่ม Push Message แจ้งเตือน (เช่น สต๊อกใกล้หมด)
- [ ] เพิ่ม AI ตอบคำถามอัจฉริยะ
- [ ] เพิ่มรายงาน PDF ส่งผ่าน LINE
