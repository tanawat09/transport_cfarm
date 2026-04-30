<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OilCompensationReason extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'reason_name',
        'requires_details',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'requires_details' => 'boolean',
        ];
    }

    public function transportJobs(): HasMany
    {
        return $this->hasMany(TransportJob::class);
    }
}
