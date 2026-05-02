<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_trip_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        DB::table('pre_trip_checklist_items')->insert([
            [
                'key' => 'engine_oil_and_fluids',
                'label' => 'ตรวจสอบน้ำมันเครื่องและของเหลวให้อยู่ในระดับปกติ',
                'sort_order' => 10,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'belt',
                'label' => 'ตรวจสอบสายพาน โดยเช็กสภาพของสายพาน ความตึงหย่อน เสียงการทำงาน',
                'sort_order' => 20,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'lights',
                'label' => 'เช็กไฟส่องสว่างให้พร้อมใช้งาน',
                'sort_order' => 30,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'leak',
                'label' => 'ตรวจดูว่ามีรอยรั่วของของเหลวต่างๆ หรือไม่ ทั้งบริเวณห้องเครื่องยนต์ และใต้ท้องรถ',
                'sort_order' => 40,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'parking_brake',
                'label' => 'ตรวจเช็กการทำงานของเบรกมือ',
                'sort_order' => 50,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'pedals_and_steering',
                'label' => 'เช็กระยะฟรีของแป้นเบรก แป้นคลัตช์ แป้นคันเร่ง และพวงมาลัย',
                'sort_order' => 60,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'tires_and_wheels',
                'label' => 'เช็กความพร้อมของยางและล้อ รวมถึงยางอะไหล่ว่าอยู่ในสภาพพร้อมใช้งาน',
                'sort_order' => 70,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_trip_checklist_items');
    }
};
