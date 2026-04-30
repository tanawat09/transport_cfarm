<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['employee_code' => 'DRV001', 'full_name' => 'สมชาย ใจดี', 'phone' => '0811111111', 'driving_license_number' => 'TH-DRV-001', 'driving_license_expiry_date' => '2027-12-31', 'status' => 'active'],
            ['employee_code' => 'DRV002', 'full_name' => 'วิชัย ขยันงาน', 'phone' => '0822222222', 'driving_license_number' => 'TH-DRV-002', 'driving_license_expiry_date' => '2028-06-30', 'status' => 'active'],
        ] as $row) {
            Driver::updateOrCreate(['employee_code' => $row['employee_code']], $row);
        }
    }
}
