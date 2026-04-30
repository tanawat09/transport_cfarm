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

    protected $fillable = [
        'registration_number',
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
            'capacity_kg' => 'decimal:2',
            'standard_fuel_rate_km_per_liter' => 'decimal:2',
        ];
    }

    public function transportJobs(): HasMany
    {
        return $this->hasMany(TransportJob::class);
    }

    public function primaryDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'primary_driver_id');
    }
}
