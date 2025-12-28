<div align="center">

# 🧺 LinenSoftTech

### Industrial Laundry Intelligence & Operations Platform

[![Laravel](https://img.shields.io/badge/Laravel-9.x-ff2d20?logo=laravel)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.4+-8892BF?logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-%2300758F?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?logo=tailwind-css)](https://tailwindcss.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

[![Build Status](https://img.shields.io/badge/build-passing-brightgreen)]()
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-blue.svg)]()
[![Code Style](https://img.shields.io/badge/code_style-psr12-ff69b4.svg)]()

**แพลตฟอร์มที่เชื่อมโยงฝั่งปฏิบัติการ ซัพพลาย พนักงาน และพลังงานของโรงซักรีดอุตสาหกรรม เพื่อให้ผู้บริหารเห็นข้อมูลแบบเรียลไทม์และตัดสินใจได้ทันที**

[Status](#-status) • [Screenshots](#-screenshots) • [Features](#-features) • [Installation](#-installation) • [Tech Stack](#-tech-stack) • [Architecture](#-architecture) • [Roadmap](#-roadmap)

</div>

---

## 🧭 Status

| Module              | Scope                                                         | State      |
| ------------------- | ------------------------------------------------------------- | ---------- |
| Operations          | Intake → Wash → Dry → Iron → Pack → Store พร้อม activity logs | ✅ MVP     |
| Customers & Billing | ลูกค้า น้ำหนักเปียก/แห้ง Billing summary และ job history      | ✅ MVP     |
| Inventory           | กลุ่มวัตถุดิบ สต๊อกคงเหลือ Import/Export audit trail          | ✅ MVP     |
| Energy Tracking     | น้ำ ไฟ แก็ส ชีวมวล น้ำมันเตา พร้อม lot tracking               | ✅ MVP     |
| Workforce           | Departments รายชื่อพนักงาน Productivity dashboards            | ✅ MVP     |
| Analytics           | Cross-domain KPIs และ trend dashboards                        | 🚧 Planned |

---

## 📸 Screenshots

<table>
  <tr>
    <td align="center"><strong>📊 Operations Dashboard</strong></td>
    <td align="center"><strong>🧵 Linen Catalog</strong></td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/operations-dashboard.png" alt="Operations Dashboard" width="420"/></td>
    <td><img src="docs/screenshots/linen-catalog.png" alt="Linen Catalog" width="420"/></td>
  </tr>
</table>

<details>
<summary>📷 More Screenshots</summary>

### Inventory & Energy Logs

<img src="docs/screenshots/inventory-energy.png" alt="Inventory & Energy Logs" width="800"/>

</details>

---

## ✨ Features

### 🧵 Product & Linen Catalogs

-   แยกผ้าทั่วไปและผ้าแก้ไข พร้อมตัวกรองชนิดผ้า สี น้ำหนัก และ summary real-time

### 👥 Customer Lifecycle

-   ข้อมูลลูกค้าโรงพยาบาล/โรงแรม บันทึกน้ำหนักเปียก-แห้ง ยอดเงิน และดึง job history ได้ทันที

### ⚙️ Operations Control

-   Job Group + Job Activity logs, บังคับให้พนักงานยืนยันตัวตนและรหัสผ่านทุกครั้ง ลดการปลอมแปลงข้อมูล

### 🔋 Energy & Sustainability

-   บันทึกพลังงานหลายประเภท พร้อม lot number และหน่วยวัด เพื่อคุมต้นทุนและตอบโจทย์ ESG

### 📦 Inventory Governance

-   Inventory groups, stock logs, import/export audit เพื่อควบคุมวัตถุดิบและสืบย้อนประวัติการใช้

### 🧑‍💼 Workforce Insights

-   รายชื่อพนักงานตามแผนก กราฟ productivity รายชั่วโมง/รายวัน และประวัติการทำงาน

---

## 🚀 Installation

```bash
# Clone repository
git clone https://github.com/your-org/linin-soft-tech.git

# Navigate
cd linin-soft-tech

# (Optional) Alias sail
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# Boot containers
./vendor/bin/sail up -d

# Install JS deps & build assets
sail yarn && sail yarn dev

# Prepare database
sail php artisan migrate:fresh --seed
```

เปิดใช้งานได้ที่ [http://localhost](http://localhost) และจัดการฐานข้อมูลผ่าน [http://localhost:8081](http://localhost:8081) (phpMyAdmin)

---

## 🛠️ Tech Stack

| Category  | Technology                                            |
| --------- | ----------------------------------------------------- |
| Framework | Laravel 9.x + Sail                                    |
| Language  | PHP 8.4+                                              |
| Database  | MySQL 8.0, Redis, Meilisearch                         |
| Frontend  | Blade, Tailwind CSS, Laravel Mix                      |
| DevOps    | Docker Desktop (macOS), Mailpit, Selenium, phpMyAdmin |
| Tooling   | Artisan, Composer, Yarn, CRUD Generator               |

---

## 📐 Architecture

```
linin-soft-tech/
├── app/                   # Domain logic (Customers, Operations, Inventory, Energy, HR)
├── database/
│   ├── migrations/        # 19 tables covering core business entities
│   └── seeders/           # User, Department, Linen, Energy, Inventory seeds
├── config/                # Service configuration
├── routes/                # Web/API routes
├── resources/
│   └── views/             # Blade templates + Tailwind
└── docker-compose.yml     # Sail services (mysql, redis, mailpit, etc.)
```

### Design Guidelines

-   ยึด Clean Architecture + SOLID + Atomic Design สำหรับ component
-   ตารางตระกูล `*_logs` เก็บ audit trail ทุกเหตุการณ์
-   Scheduler + system cron สำหรับงานอัตโนมัติรายวัน

---

## 🗂️ Project Structure Highlights

-   **Operations:** `job_groups`, `jobs`, `job_group_activity_logs`, `job_activity_logs`, `employee_operation_logs`
-   **Inventory:** `inventory_groups`, `inventories`, `inventory_stock_logs`
-   **Energy:** `energy_resources`, `energy_resource_logs`
-   **HR:** `departments`, `employees`
-   **Customers:** `customers`, `jobs`

---

## 🔄 Daily Workflow

| Task               | Command                                                                 |
| ------------------ | ----------------------------------------------------------------------- |
| Start stack        | `./vendor/bin/sail up` / `./vendor/bin/sail up -d`                      |
| Stop stack         | `./vendor/bin/sail stop`                                                |
| Yarn install/build | `sail yarn`, `sail yarn add <pkg>`, `sail yarn dev`                     |
| Artisan / PHP      | `./vendor/bin/sail artisan <cmd>`, `./vendor/bin/sail php --version`    |
| CRUD scaffolding   | `sail php artisan make:migration ...`, `sail php artisan make:crud ...` |
| Events & Enums     | `php artisan make:enum ...`, `sail php artisan make:event ...`          |

---

## ⏱️ Scheduling & Automation

1. สร้างคำสั่ง `sail php artisan make:command DaillyReportCron --command=dailyReport:cron`
2. เพิ่ม logic + logging ใน `handle()`
3. ลงทะเบียนผ่าน `$schedule->command(DaillyReportCron::class)->daily();`
4. ทดสอบด้วย `sail php artisan schedule:run` หรือ `schedule:work`
5. ติดตั้ง cron: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`

---

## 🎯 Roadmap

-   [x] Operations pipeline & workforce tracking
-   [x] Inventory + energy logging
-   [x] Seeder-driven bootstrap data
-   [ ] Advanced analytics dashboards (in progress)
-   [ ] External integrations (IoT meters, accounting API)

---

## 📄 License

Distributed under the MIT License. See [LICENSE](LICENSE) for more information.

---

<div align="center">

Maintained with ❤️ by the LinenSoftTech team.  
Star ⭐ the repo if this project helps your industrial laundry operations!

</div>
