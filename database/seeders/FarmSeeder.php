<?php

namespace Database\Seeders;

use App\Models\Farm;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['farm_name' => 'ฟาร์มเจริญทรัพย์ 1', 'owner_name' => 'นายเกษม รุ่งเรือง', 'address' => 'อำเภอบ้านหมอ จังหวัดสระบุรี', 'phone' => '0833333333'],
            ['farm_name' => 'ฟาร์มทุ่งทอง', 'owner_name' => 'นางสาวกมลวรรณ ใจงาม', 'address' => 'อำเภอพัฒนานิคม จังหวัดลพบุรี', 'phone' => '0844444444'],
        ] as $row) {
            Farm::updateOrCreate(['farm_name' => $row['farm_name']], $row);
        }
    }
}
