<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreTripInspection extends Model
{
    use HasFactory;

    public const STATUS_PASS = 'pass';
    public const STATUS_FAIL = 'fail';

    public const CHECK_FIELDS = [
        'engine_oil_and_fluids_status',
        'belt_status',
        'lights_status',
        'leak_status',
        'parking_brake_status',
        'pedals_and_steering_status',
        'tires_and_wheels_status',
    ];

    public const LEGACY_CHECK_KEYS = [
        'engine_oil_and_fluids',
        'belt',
        'lights',
        'leak',
        'parking_brake',
        'pedals_and_steering',
        'tires_and_wheels',
    ];

    protected $fillable = [
        'inspection_date',
        'inspection_time',
        'vehicle_id',
        'driver_id',
        'user_id',
        'odometer_km',
        'engine_oil_and_fluids_status',
        'engine_oil_and_fluids_note',
        'belt_status',
        'belt_note',
        'lights_status',
        'lights_note',
        'leak_status',
        'leak_note',
        'parking_brake_status',
        'parking_brake_note',
        'pedals_and_steering_status',
        'pedals_and_steering_note',
        'tires_and_wheels_status',
        'tires_and_wheels_note',
        'checklist_results',
        'is_ready_to_drive',
        'overall_note',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'date',
            'odometer_km' => 'decimal:2',
            'is_ready_to_drive' => 'boolean',
            'checklist_results' => 'array',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PASS => 'ผ่าน',
            self::STATUS_FAIL => 'ไม่ผ่าน',
        ];
    }

    public static function evaluationItems(): array
    {
        $items = PreTripChecklistItem::query()
            ->active()
            ->ordered()
            ->pluck('label', 'key')
            ->all();

        if ($items !== []) {
            return $items;
        }

        return [
            'engine_oil_and_fluids' => 'ตรวจสอบน้ำมันเครื่องและของเหลวให้อยู่ในระดับปกติ',
            'belt' => 'ตรวจสอบสายพาน สภาพ ความตึงหย่อน และเสียงการทำงาน',
            'lights' => 'เช็กไฟส่องสว่างให้พร้อมใช้งาน',
            'leak' => 'ตรวจดูรอยรั่วของของเหลวในห้องเครื่องและใต้ท้องรถ',
            'parking_brake' => 'ตรวจเช็กการทำงานของเบรกมือ',
            'pedals_and_steering' => 'เช็กระยะฟรีของแป้นเบรก แป้นคลัตช์ แป้นคันเร่ง และพวงมาลัย',
            'tires_and_wheels' => 'เช็กความพร้อมของยาง ล้อ และยางอะไหล่',
        ];
    }

    public static function checkFieldLabel(string $field): string
    {
        $key = str($field)->beforeLast('_status')->toString();
        $configuredLabel = PreTripChecklistItem::where('key', $key)->value('label');

        if ($configuredLabel) {
            return $configuredLabel;
        }

        return [
            'engine_oil_and_fluids_status' => 'น้ำมันเครื่องและของเหลว',
            'belt_status' => 'สายพาน',
            'lights_status' => 'ไฟส่องสว่าง',
            'leak_status' => 'รอยรั่วของของเหลว',
            'parking_brake_status' => 'เบรกมือ',
            'pedals_and_steering_status' => 'แป้นเบรก คลัตช์ คันเร่ง และพวงมาลัย',
            'tires_and_wheels_status' => 'ยางและล้อ',
        ][$field] ?? $field;
    }

    public function checklistItemsForDisplay(): array
    {
        $results = $this->checklist_results;

        if (is_array($results) && $results !== []) {
            return collect($results)
                ->map(function (array $result, string $key) {
                    return [
                        'key' => $key,
                        'label' => $result['label'] ?? $key,
                        'status' => $result['status'] ?? null,
                        'note' => $result['note'] ?? null,
                    ];
                })
                ->values()
                ->all();
        }

        return collect(self::evaluationItems())
            ->map(function (string $label, string $key) {
                return [
                    'key' => $key,
                    'label' => $label,
                    'status' => $this->{$key . '_status'},
                    'note' => $this->{$key . '_note'},
                ];
            })
            ->values()
            ->all();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(?string $value): string
    {
        return self::statusOptions()[$value] ?? $value;
    }
}
