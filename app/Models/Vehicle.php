<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const USAGE_LOG_VEHICLE_TYPES = [
        'รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน',
        'รถยนต์บรรทุกส่วนบุคคล',
        'รถไถ',
        'รถไถ คูโบต้า',
    ];

    public const PRE_TRIP_INSPECTION_QR_EXCLUDED_TYPES = [
        'รถยนต์บรรทุกส่วนบุคคล',
        'รถไถ คูโบต้า',
        'รถยนต์นั่งส่วนบุคคลไม่เกิน 7 คน',
    ];

    protected $fillable = [
        'registration_number',
        'registered_at',
        'vehicle_type',
        'towing_vehicle',
        'brand',
        'model',
        'capacity_kg',
        'standard_fuel_rate_km_per_liter',
        'primary_driver_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'date',
            'capacity_kg' => 'decimal:2',
            'standard_fuel_rate_km_per_liter' => 'decimal:2',
        ];
    }

    public function transportJobs(): HasMany
    {
        return $this->hasMany(TransportJob::class);
    }

    public function preTripInspections(): HasMany
    {
        return $this->hasMany(PreTripInspection::class);
    }

    public function tireRegistrations(): HasMany
    {
        return $this->hasMany(TireRegistration::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(VehicleUsageLog::class);
    }

    public function vehicleDocuments(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function primaryDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'primary_driver_id');
    }

    public function inspectionQrUrl(): string
    {
        return url('/pre-trip-inspections/create?vehicle_id=' . $this->id);
    }

    public function usageLogQrUrl(): string
    {
        return url('/vehicle-usage-logs/create?vehicle_id=' . $this->id);
    }

    public function supportsUsageLog(): bool
    {
        return in_array($this->vehicle_type, self::USAGE_LOG_VEHICLE_TYPES, true);
    }

    public function supportsPreTripInspectionQr(): bool
    {
        return ! in_array($this->vehicle_type, self::PRE_TRIP_INSPECTION_QR_EXCLUDED_TYPES, true);
    }
}
