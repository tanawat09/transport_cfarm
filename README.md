# ระบบบริหารรถขนส่งอาหารไก่

โปรเจกต์นี้เป็นตัวอย่างระบบเว็บแอปสำหรับบริษัทขนส่งอาหารไก่ พัฒนาด้วยแนวทาง Laravel 11 + MySQL + Blade + Bootstrap 5 โดยออกแบบให้รองรับข้อมูลรถ, พนักงานขับ, ฟาร์ม, คู่สัญญา, มาตรฐานเส้นทาง, เที่ยวขนส่ง และรายงานสรุปพร้อม Export Excel/PDF

## 1. วิเคราะห์ Requirement

### ขอบเขตหลัก
- ระบบเข้าสู่ระบบพร้อมสิทธิ์ `admin` และ `operator`
- CRUD สำหรับ `vehicles`, `drivers`, `farms`, `vendors`, `route_standards`
- บันทึกเที่ยวขนส่งพร้อมคำนวณระยะทาง, น้ำมันอนุมัติ, ค่าน้ำมัน, ส่วนต่างน้ำมัน และส่วนต่างระยะทาง
- รายงานรายวัน, ตามช่วงวันที่, ตามรถ, ตามพนักงานขับ, ตามฟาร์ม, ตามคู่สัญญา
- ส่งออก Excel และ PDF

### Business Logic สำคัญ
- เมื่อเลือกฟาร์ม + คู่สัญญา ระบบจะค้นหา `route_standards` ที่ active และโหลด `company_oil_liters` กับ `standard_distance_km` อัตโนมัติ
- ถ้าไม่พบ route standard ระบบจะแจ้งเตือนหน้า form และฝั่ง server จะไม่ยอมบันทึก
- ถ้า `oil_compensation_liters > 0` ต้องเลือกเหตุผลชดเชย
- ถ้าเหตุผลชดเชยกำหนดว่า `requires_details = true` หรือเป็น `อื่นๆ` ต้องกรอกรายละเอียดชดเชย
- `odometer_end >= odometer_start`
- `actual_oil_liters >= 0`
- `oil_price_per_liter >= 0`
- เอกสารรันเลขรูปแบบ `TRN-YYYYMMDD-0001`

## 2. ER Diagram แบบข้อความ

```text
users
  id PK
  role (admin, operator)

vehicles
  id PK
  registration_number UNIQUE
  hasMany transport_jobs

drivers
  id PK
  employee_code UNIQUE
  hasMany transport_jobs

farms
  id PK
  hasMany route_standards
  hasMany transport_jobs

vendors
  id PK
  hasMany route_standards
  hasMany transport_jobs

route_standards
  id PK
  farm_id FK -> farms.id
  vendor_id FK -> vendors.id
  belongsTo farm
  belongsTo vendor
  hasMany transport_jobs

oil_compensation_reasons
  id PK
  hasMany transport_jobs

transport_jobs
  id PK
  vehicle_id FK -> vehicles.id
  driver_id FK -> drivers.id
  farm_id FK -> farms.id
  vendor_id FK -> vendors.id
  route_standard_id FK -> route_standards.id
  oil_compensation_reason_id FK -> oil_compensation_reasons.id nullable
  belongsTo vehicle
  belongsTo driver
  belongsTo farm
  belongsTo vendor
  belongsTo route_standard
  belongsTo oil_compensation_reason
```

## 3. สรุปโครงสร้างตาราง

### users
- `name`, `email`, `password`, `role`
- index: `email`, `role`

### vehicles
- `registration_number`, `brand`, `model`, `capacity_kg`, `standard_fuel_rate_km_per_liter`, `status`, `notes`
- index: `registration_number`, `status`
- soft deletes: yes

### drivers
- `employee_code`, `full_name`, `phone`, `driving_license_number`, `driving_license_expiry_date`, `status`, `notes`
- index: `employee_code`, `driving_license_number`, `driving_license_expiry_date`, `status`
- soft deletes: yes

### farms
- `farm_name`, `owner_name`, `address`, `phone`, `notes`
- index: `farm_name`
- soft deletes: yes

### vendors
- `vendor_name`, `details`, `status`
- index: `vendor_name`, `status`
- soft deletes: yes

### route_standards
- `farm_id`, `vendor_id`, `company_oil_liters`, `standard_distance_km`, `notes`, `status`
- index: `farm_id`, `vendor_id`, `status`
- soft deletes: yes

### oil_compensation_reasons
- `reason_name`, `requires_details`, `status`, `notes`
- index: `reason_name`, `status`
- soft deletes: yes

### transport_jobs
- `transport_date`, `document_no`, `vehicle_id`, `driver_id`, `farm_id`, `vendor_id`, `route_standard_id`
- `food_weight_kg`, `odometer_start`, `odometer_end`, `actual_distance_km`
- `standard_distance_km`, `company_oil_liters`, `oil_compensation_liters`, `oil_compensation_reason_id`, `oil_compensation_details`
- `approved_oil_liters`, `actual_oil_liters`, `oil_price_per_liter`, `total_oil_cost`
- `oil_difference_liters`, `distance_difference_km`, `average_fuel_rate_km_per_liter`, `notes`
- index: `transport_date`, `document_no`, foreign keys, composite indexes per dimension + date
- soft deletes: yes

## 4. สูตรคำนวณที่ใช้ในระบบ

- `actual_distance_km = odometer_end - odometer_start`
- `approved_oil_liters = company_oil_liters + oil_compensation_liters`
- `oil_difference_liters = actual_oil_liters - approved_oil_liters`
- `average_fuel_rate_km_per_liter = actual_distance_km / actual_oil_liters`
- `total_oil_cost = actual_oil_liters * oil_price_per_liter`
- `distance_difference_km = actual_distance_km - standard_distance_km`

Implementation อยู่ใน `app/Services/TransportJobCalculationService.php`

## 5. โครงสร้างไฟล์สำคัญ

- `database/migrations/*` ออกแบบ schema ทั้งระบบ
- `app/Models/*` relation และ cast fields
- `app/Http/Requests/*` validation ของแต่ละโมดูล
- `app/Services/RunningNumberService.php` สร้าง running number
- `app/Services/TransportJobCalculationService.php` สูตรคำนวณ
- `app/Services/ReportService.php` query และ summary ของรายงาน
- `app/Exports/TransportJobsReportExport.php` export excel
- `app/Http/Controllers/*` CRUD, dashboard, report, auth, AJAX lookup
- `resources/views/*` Blade views ทั้งระบบ

## 6. วิธีติดตั้งและใช้งาน

### Requirement สำหรับเครื่องที่จะรันจริง
- PHP 8.2+
- Composer 2+
- MySQL 8+
- Extensions ที่ Laravel และ package ต้องใช้ เช่น `mbstring`, `openssl`, `pdo_mysql`, `zip`, `gd`

### ขั้นตอนติดตั้ง
1. ติดตั้ง dependencies
   ```bash
   composer install
   ```
2. คัดลอก environment file
   ```bash
   copy .env.example .env
   ```
3. ตั้งค่าฐานข้อมูลใน `.env`
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=transport_cfarm
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. สร้าง app key
   ```bash
   php artisan key:generate
   ```
5. migrate และ seed ข้อมูลตัวอย่าง
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
6. รันระบบ
   ```bash
   php artisan serve
   ```
7. เข้าใช้งานที่ `http://127.0.0.1:8000`

### บัญชีทดสอบจาก Seeder
- admin
  - email: `admin@cfarm.local`
  - password: `password`
- operator
  - email: `operator@cfarm.local`
  - password: `password`

## 7. สิทธิ์การใช้งาน

- `admin`: จัดการ master data, route standard, บันทึกเที่ยว, ดูรายงาน
- `operator`: dashboard, บันทึกเที่ยว, ดูรายการเที่ยว, ดูรายงาน

## 8. หมายเหตุด้านแพ็กเกจ

ใน `composer.json` ได้เตรียม package ไว้แล้ว
- `maatwebsite/excel` สำหรับ Excel export
- `barryvdh/laravel-dompdf` สำหรับ PDF export

## 9. ข้อจำกัดของ workspace นี้

Environment ที่ใช้สร้างโค้ดรอบนี้ไม่มี network สำหรับดาวน์โหลด dependency และมี PHP ใน path เป็น 8.0 จึงยังไม่สามารถรัน `composer install` หรือทดสอบจริงใน workspace นี้ได้ แต่ source code ถูกจัดให้สอดคล้องกับ Laravel 11 / PHP 8.2+ สำหรับนำไปรันบนเครื่องที่พร้อมใช้งานได้ทันที
#   t r a n s p o r t _ c f a r m  
 