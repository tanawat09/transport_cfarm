<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\RouteStandard;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class RouteStandardSeeder extends Seeder
{
    public function run(): void
    {
        $farmA = Farm::query()->where('farm_name', 'ฟาร์มเจริญทรัพย์ 1')->first();
        $farmB = Farm::query()->where('farm_name', 'ฟาร์มทุ่งทอง')->first();
        $vendorA = Vendor::query()->where('vendor_name', 'บริษัท ซีเฟด จำกัด')->first();
        $vendorB = Vendor::query()->where('vendor_name', 'บริษัท โกลเด้นฟีด จำกัด')->first();

        foreach ([
            ['farm_id' => $farmA?->id, 'vendor_id' => $vendorA?->id, 'company_oil_liters' => 52, 'standard_distance_km' => 175, 'status' => 'active'],
            ['farm_id' => $farmB?->id, 'vendor_id' => $vendorB?->id, 'company_oil_liters' => 61, 'standard_distance_km' => 215, 'status' => 'active'],
        ] as $row) {
            RouteStandard::updateOrCreate(
                ['farm_id' => $row['farm_id'], 'vendor_id' => $row['vendor_id']],
                $row
            );
        }
    }
}
