<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class VehicleQrToken extends Model
{
    use HasFactory;

    public const TYPE_INSPECTION = 'inspection';
    public const TYPE_USAGE = 'usage';
    public const TYPE_TRACTOR_USAGE_INSPECTION = 'tractor_usage_inspection';

    protected $fillable = [
        'vehicle_id',
        'access_type',
        'token',
        'pin_code',
        'is_active',
        'expires_at',
        'last_verified_at',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
            'last_verified_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public static function issueFor(Vehicle $vehicle, string $accessType): self
    {
        $token = static::query()->firstOrNew([
            'vehicle_id' => $vehicle->id,
            'access_type' => $accessType,
        ]);

        if (! $token->exists || blank($token->token) || blank($token->pin_code)) {
            $token->fill(static::freshCredentials());
            $token->is_active = true;
            $token->save();
        }

        return $token;
    }

    public static function freshCredentials(): array
    {
        $pin = (string) random_int(100000, 999999);

        return [
            'token' => Str::random(64),
            'pin_code' => Crypt::encryptString($pin),
        ];
    }

    public function decryptedPin(): string
    {
        return Crypt::decryptString($this->pin_code);
    }

    public function verifyPin(string $pin): bool
    {
        return hash_equals($this->decryptedPin(), trim($pin));
    }

    public function publicUrl(): string
    {
        return route('public.vehicle-qr.access', $this->token);
    }

    public function targetFormUrl(): string
    {
        return match ($this->access_type) {
            self::TYPE_USAGE => route('public.vehicle-qr.usage.form', $this->token),
            self::TYPE_TRACTOR_USAGE_INSPECTION => route('public.vehicle-qr.tractor-usage-inspection.form', $this->token),
            default => route('public.vehicle-qr.inspection.form', $this->token),
        };
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isAvailable(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    public function regenerate(): array
    {
        $credentials = static::freshCredentials();

        $this->forceFill([
            'token' => $credentials['token'],
            'pin_code' => $credentials['pin_code'],
            'is_active' => true,
            'last_verified_at' => null,
            'last_used_at' => null,
        ])->save();

        return [
            'token' => $this,
            'plain_pin' => Crypt::decryptString($credentials['pin_code']),
        ];
    }

    public function markVerified(): void
    {
        $this->forceFill(['last_verified_at' => now()])->save();
    }

    public function markUsed(): void
    {
        $this->forceFill(['last_used_at' => now()])->save();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(VehicleQrAccessLog::class);
    }
}
