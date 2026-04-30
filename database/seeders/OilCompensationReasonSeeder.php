<?php

namespace Database\Seeders;

use App\Models\OilCompensationReason;
use Illuminate\Database\Seeder;

class OilCompensationReasonSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['reason_name' => 'รถติดหนัก', 'requires_details' => false, 'status' => 'active'],
            ['reason_name' => 'ทางอ้อมจากเหตุฉุกเฉิน', 'requires_details' => true, 'status' => 'active'],
            ['reason_name' => 'อื่นๆ', 'requires_details' => true, 'status' => 'active'],
        ] as $row) {
            OilCompensationReason::updateOrCreate(['reason_name' => $row['reason_name']], $row);
        }
    }
}
