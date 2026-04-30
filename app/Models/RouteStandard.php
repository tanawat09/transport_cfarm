<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteStandard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'farm_id',
        'vendor_id',
        'company_oil_liters',
        'standard_distance_km',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'company_oil_liters' => 'decimal:2',
            'standard_distance_km' => 'decimal:2',
        ];
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function transportJobs(): HasMany
    {
        return $this->hasMany(TransportJob::class);
    }
}
