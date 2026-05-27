<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class TractorUsageInspection extends Model
{
    use HasFactory;

    public const STATUS_PASS = 'pass';
    public const STATUS_FAIL = 'fail';

    protected $fillable = [
        'inspection_date',
        'inspection_time',
        'vehicle_id',
        'driver_id',
        'farm_id',
        'user_id',
        'working_hours',
        'checklist_results',
        'is_ready_for_use',
        'overall_note',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'date',
            'working_hours' => 'decimal:2',
            'checklist_results' => 'array',
            'is_ready_for_use' => 'boolean',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PASS => 'ผ่าน',
            self::STATUS_FAIL => 'ไม่ผ่าน',
        ];
    }

    public static function checklistGroups(): array
    {
        if (Schema::hasTable('tractor_usage_checklist_items')) {
            $items = TractorUsageChecklistItem::query()
                ->active()
                ->ordered()
                ->get();

            if ($items->isNotEmpty()) {
                return $items
                    ->groupBy('group_key')
                    ->map(function ($groupItems) {
                        $first = $groupItems->first();

                        return [
                            'key' => $first->group_key,
                            'label' => $first->group_label,
                            'items' => $groupItems
                                ->map(fn (TractorUsageChecklistItem $item) => [
                                    'key' => $item->key,
                                    'label' => $item->label,
                                ])
                                ->values()
                                ->all(),
                        ];
                    })
                    ->values()
                    ->all();
            }
        }

        return self::defaultChecklistGroups();
    }

    public static function defaultChecklistGroups(): array
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

    public static function checklistItems(): array
    {
        return collect(self::checklistGroups())
            ->flatMap(fn (array $group) => $group['items'])
            ->values()
            ->all();
    }

    public function checklistItemsForDisplay(): array
    {
        $results = $this->checklist_results ?? [];

        return collect(self::checklistGroups())
            ->map(function (array $group) use ($results) {
                return [
                    'key' => $group['key'],
                    'label' => $group['label'],
                    'items' => collect($group['items'])->map(function (array $item) use ($results) {
                        $result = $results[$item['key']] ?? [];

                        return [
                            'key' => $item['key'],
                            'label' => $item['label'],
                            'status' => $result['status'] ?? null,
                            'note' => $result['note'] ?? null,
                        ];
                    })->all(),
                ];
            })
            ->all();
    }

    public function statusLabel(?string $value): string
    {
        return self::statusOptions()[$value] ?? (string) $value;
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
