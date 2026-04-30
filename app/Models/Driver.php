<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_code',
        'full_name',
        'phone',
        'driving_license_number',
        'driving_license_expiry_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'driving_license_expiry_date' => 'date',
        ];
    }

    public function transportJobs(): HasMany
    {
        return $this->hasMany(TransportJob::class);
    }

    public function assignedVehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'primary_driver_id');
    }

    public function preTripInspections(): HasMany
    {
        return $this->hasMany(PreTripInspection::class);
    }
}
