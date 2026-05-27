<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tractor_usage_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group_key');
            $table->string('group_label');
            $table->string('label', 1000);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['group_key', 'sort_order']);
            $table->index(['is_active', 'sort_order']);
        });

        $now = now();
        $sort = 10;

        foreach ($this->defaultGroups() as $group) {
            foreach ($group['items'] as $item) {
                DB::table('tractor_usage_checklist_items')->insert([
                    'key' => $item['key'],
                    'group_key' => $group['key'],
                    'group_label' => $group['label'],
                    'label' => $item['label'],
                    'sort_order' => $sort,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $sort += 10;
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tractor_usage_checklist_items');
    }

    private function defaultGroups(): array
    {
        return [
            [
                'key' => 'engine_system',
                'label' => 'ระบบเครื่องยนต์',
                'items' => [
                    ['key' => 'battery', 'label' => 'แบตเตอรี่ (ตรวจสอบขั้ว/น้ำกลั่น)'],
                    ['key' => 'dust_filter', 'label' => 'กรองฝุ่น (เป่าลมทำความสะอาด)'],
                    ['key' => 'engine_oil', 'label' => 'น้ำมันเครื่อง'],
                    ['key' => 'radiator_water_level', 'label' => 'ระดับน้ำในหม้อน้ำ'],
                ],
            ],
            [
                'key' => 'brake_system',
                'label' => 'ระบบเบรค',
                'items' => [
                    ['key' => 'brake_fluid_level', 'label' => 'ระดับน้ำมันเบรค'],
                ],
            ],
            [
                'key' => 'hydraulic_system',
                'label' => 'ระบบ Hydraulic',
                'items' => [
                    ['key' => 'hydraulic_hose', 'label' => 'ระบบสาย Hydraulic'],
                    ['key' => 'hydraulic_oil', 'label' => 'น้ำมัน Hydraulic'],
                ],
            ],
            [
                'key' => 'clutch_system',
                'label' => 'ระบบคลัช',
                'items' => [
                    ['key' => 'clutch_and_acceleration', 'label' => 'คลัชหมดไหม/การเร่งเครื่อง'],
                ],
            ],
            [
                'key' => 'air_filter_system',
                'label' => 'ระบบกรองอากาศ',
                'items' => [
                    ['key' => 'paper_filter', 'label' => 'กรองกระดาษ'],
                    ['key' => 'air_filter', 'label' => 'กรองอากาศ'],
                ],
            ],
            [
                'key' => 'front_blade',
                'label' => 'ใบมีดดันดินด้านหน้า',
                'items' => [
                    ['key' => 'blade_wear', 'label' => 'การสึกของใบมีด/น็อตยึดต่างๆ'],
                    ['key' => 'front_hydraulic_cylinder', 'label' => 'กระบอกไฮดรอลิคหน้า'],
                ],
            ],
            [
                'key' => 'auger_and_bucket',
                'label' => 'ชุดผ้าบุ้งกี๋ ตักขี้เลื่อย',
                'items' => [
                    ['key' => 'spinning_gear_bearing', 'label' => 'ลูกปืนเฟืองปั่น'],
                    ['key' => 'chain_gear_teeth', 'label' => 'ฟันเฟือง (ระบบโซ่)'],
                ],
            ],
            [
                'key' => 'tire_system',
                'label' => 'ยาง',
                'items' => [
                    ['key' => 'tire_tread', 'label' => 'ดอกยาง (สึก/ชำรุด)'],
                ],
            ],
            [
                'key' => 'cleanliness',
                'label' => 'ความสะอาด - ทั้งคัน',
                'items' => [
                    ['key' => 'vehicle_cleanliness', 'label' => 'สะอาด/ไม่สะอาด'],
                ],
            ],
        ];
    }
};
