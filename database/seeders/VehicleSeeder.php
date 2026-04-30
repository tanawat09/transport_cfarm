<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = Driver::query()
            ->whereIn('employee_code', ['DRV001', 'DRV002'])
            ->pluck('id', 'employee_code');

        foreach ([
            ['registration_number' => '70-1234', 'brand' => 'Hino', 'model' => '500', 'capacity_kg' => 12000, 'standard_fuel_rate_km_per_liter' => 3.80, 'primary_driver_id' => $drivers['DRV001'] ?? null, 'status' => 'active'],
            ['registration_number' => '71-5678', 'brand' => 'Isuzu', 'model' => 'FTR', 'capacity_kg' => 10000, 'standard_fuel_rate_km_per_liter' => 4.10, 'primary_driver_id' => $drivers['DRV002'] ?? null, 'status' => 'active'],
        ] as $row) {
            Vehicle::updateOrCreate(['registration_number' => $row['registration_number']], $row);
        }
    }
}
