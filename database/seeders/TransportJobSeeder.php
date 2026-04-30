<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Farm;
use App\Models\OilCompensationReason;
use App\Models\RouteStandard;
use App\Models\TransportJob;
use App\Models\Vehicle;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class TransportJobSeeder extends Seeder
{
    public function run(): void
    {
        $vehicle = Vehicle::query()->where('registration_number', '70-1234')->first();
        $driver = Driver::query()->where('employee_code', 'DRV001')->first();
        $farm = Farm::query()->where('farm_name', 'ฟาร์มเจริญทรัพย์ 1')->first();
        $vendor = Vendor::query()->where('vendor_name', 'บริษัท ซีเฟด จำกัด')->first();
        $routeStandard = RouteStandard::query()->where('farm_id', $farm?->id)->where('vendor_id', $vendor?->id)->first();
        $reason = OilCompensationReason::query()->where('reason_name', 'รถติดหนัก')->first();

        TransportJob::updateOrCreate(
            ['document_no' => 'TRN-20260326-0001'],
            [
                'transport_date' => '2026-03-26',
                'vehicle_id' => $vehicle?->id,
                'driver_id' => $driver?->id,
                'farm_id' => $farm?->id,
                'vendor_id' => $vendor?->id,
                'route_standard_id' => $routeStandard?->id,
                'food_weight_kg' => 8000,
                'odometer_start' => 10250,
                'odometer_end' => 10432,
                'actual_distance_km' => 182,
                'standard_distance_km' => 175,
                'company_oil_liters' => 52,
                'oil_compensation_liters' => 3,
                'oil_compensation_reason_id' => $reason?->id,
                'oil_compensation_details' => 'การจราจรติดขัดบริเวณเส้นทางเข้าฟาร์ม',
                'approved_oil_liters' => 55,
                'actual_oil_liters' => 57,
                'oil_price_per_liter' => 31.50,
                'total_oil_cost' => 1795.50,
                'oil_difference_liters' => 2,
                'oil_difference_amount' => 63.00,
                'distance_difference_km' => 7,
                'average_fuel_rate_km_per_liter' => 3.19,
                'notes' => 'เที่ยวตัวอย่างสำหรับทดสอบระบบ',
            ]
        );
    }
}
