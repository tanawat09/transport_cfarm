<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'driver_name',
        'usage_date',
        'usage_month',
        'odometer_start',
        'odometer_end',
        'distance_km',
        'purpose',
        'destination',
        'fuel_liters',
        'fuel_price_per_liter',
        'fuel_total_amount',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'usage_date' => 'date',
            'odometer_start' => 'decimal:2',
            'odometer_end' => 'decimal:2',
            'distance_km' => 'decimal:2',
            'fuel_liters' => 'decimal:2',
            'fuel_price_per_liter' => 'decimal:2',
            'fuel_total_amount' => 'decimal:2',
        ];
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
}
