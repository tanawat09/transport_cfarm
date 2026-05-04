<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleQrAccessLog extends Model
{
    use HasFactory;

    public const EVENT_CHALLENGE_VIEWED = 'challenge_viewed';
    public const EVENT_PIN_VERIFIED = 'pin_verified';
    public const EVENT_PIN_FAILED = 'pin_failed';
    public const EVENT_FORM_VIEWED = 'form_viewed';
    public const EVENT_FORM_SUBMITTED = 'form_submitted';
    public const EVENT_TOKEN_REGENERATED = 'token_regenerated';
    public const EVENT_TOKEN_DISABLED = 'token_disabled';
    public const EVENT_TOKEN_ENABLED = 'token_enabled';

    protected $fillable = [
        'vehicle_qr_token_id',
        'vehicle_id',
        'access_type',
        'event_type',
        'ip_address',
        'user_agent',
        'payload',
        'happened_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'happened_at' => 'datetime',
        ];
    }

    public function qrToken(): BelongsTo
    {
        return $this->belongsTo(VehicleQrToken::class, 'vehicle_qr_token_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
