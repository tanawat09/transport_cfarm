<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['vendor_name' => 'บริษัท ซีเฟด จำกัด', 'details' => 'คู่สัญญาหลักภาคกลาง', 'status' => 'active'],
            ['vendor_name' => 'บริษัท โกลเด้นฟีด จำกัด', 'details' => 'คู่สัญญาเส้นทางภาคตะวันออก', 'status' => 'active'],
        ] as $row) {
            Vendor::updateOrCreate(['vendor_name' => $row['vendor_name']], $row);
        }
    }
}
