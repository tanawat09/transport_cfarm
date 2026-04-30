<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vendor_name',
        'details',
        'status',
    ];

    public function routeStandards(): HasMany
    {
        return $this->hasMany(RouteStandard::class);
    }

    public function transportJobs(): HasMany
    {
        return $this->hasMany(TransportJob::class);
    }
}
