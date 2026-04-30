<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TireRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'tire_serial_number',
        'tire_position',
        'installed_at',
        'installed_mileage_km',
        'standard_replacement_distance_km',
        'removed_mileage_km',
        'distance_run_km',
        'tread_depth_mm',
        'tire_size',
        'brand',
        'condition_status',
        'vendor_name',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'installed_at' => 'date',
            'installed_mileage_km' => 'decimal:2',
            'standard_replacement_distance_km' => 'decimal:2',
            'removed_mileage_km' => 'decimal:2',
            'distance_run_km' => 'decimal:2',
            'tread_depth_mm' => 'decimal:2',
        ];
    }

    public static function conditionOptions(): array
    {
        return [
            'normal' => 'ปกติ',
            'warning' => 'เฝ้าระวัง',
            'replace' => 'ต้องเปลี่ยน',
            'repair' => 'ซ่อม',
            'empty' => 'ไม่มีข้อมูล',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
