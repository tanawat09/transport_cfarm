<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DriverSeeder::class,
            VehicleSeeder::class,
            FarmSeeder::class,
            VendorSeeder::class,
            RouteStandardSeeder::class,
            OilCompensationReasonSeeder::class,
            TransportJobSeeder::class,
        ]);
    }
}
