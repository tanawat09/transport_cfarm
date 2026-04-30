<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportJob extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'transport_date',
        'document_no',
        'vehicle_id',
        'driver_id',
        'farm_id',
        'vendor_id',
        'route_standard_id',
        'food_weight_kg',
        'odometer_start',
        'odometer_end',
        'other_job_description',
        'other_job_odometer_start',
        'other_job_odometer_end',
        'other_job_distance_km',
        'actual_distance_km',
        'standard_distance_km',
        'company_oil_liters',
        'oil_compensation_liters',
        'oil_compensation_reason_id',
        'oil_compensation_details',
        'approved_oil_liters',
        'actual_oil_liters',
        'vehicle_screen_oil_liters',
        'oil_price_per_liter',
        'total_oil_cost',
        'oil_difference_liters',
        'oil_difference_amount',
        'distance_difference_km',
        'average_fuel_rate_km_per_liter',
        'calculation_status',
        'calculation_note',
        'calculated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transport_date' => 'date',
            'food_weight_kg' => 'decimal:2',
            'odometer_start' => 'decimal:2',
            'odometer_end' => 'decimal:2',
            'other_job_odometer_start' => 'decimal:2',
            'other_job_odometer_end' => 'decimal:2',
            'other_job_distance_km' => 'decimal:2',
            'actual_distance_km' => 'decimal:2',
            'standard_distance_km' => 'decimal:2',
            'company_oil_liters' => 'decimal:2',
            'oil_compensation_liters' => 'decimal:2',
            'approved_oil_liters' => 'decimal:2',
            'actual_oil_liters' => 'decimal:2',
            'vehicle_screen_oil_liters' => 'decimal:2',
            'oil_price_per_liter' => 'decimal:2',
            'total_oil_cost' => 'decimal:2',
            'oil_difference_liters' => 'decimal:2',
            'oil_difference_amount' => 'decimal:2',
            'distance_difference_km' => 'decimal:2',
            'average_fuel_rate_km_per_liter' => 'decimal:2',
            'calculated_at' => 'datetime',
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

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function routeStandard(): BelongsTo
    {
        return $this->belongsTo(RouteStandard::class);
    }

    public function oilCompensationReason(): BelongsTo
    {
        return $this->belongsTo(OilCompensationReason::class);
    }
}
